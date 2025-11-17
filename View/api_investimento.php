<?php
/**
 * api_investimento.php
 * API para gerenciar investimentos com integração ao banco
 */

header('Content-Type: application/json; charset=utf-8');

// Carregar o banco de dados
$configPath = dirname(__DIR__) . '/config/Database.php';
$modelsPath = dirname(__DIR__) . '/app/Models/';

if (!file_exists($configPath)) {
    http_response_code(500);
    die(json_encode(['erro' => 'Database.php não encontrado']));
}

require_once $configPath;

// Carregar modelos
require_once $modelsPath . 'Ativo.php';
require_once $modelsPath . 'InvestimentoTransacao.php';

$acao = $_GET['acao'] ?? null;
$userId = $_GET['user_id'] ?? 1;

try {
    // Inicializar banco
    $database = new Database();
    $db = $database->connect();
    
    switch ($acao) {
        // Carteira do usuário
        case 'carteira':
            $sql = "SELECT 
                        a.ativo_id,
                        a.asset_symbol,
                        a.asset_name,
                        a.asset_type,
                        SUM(CASE WHEN t.tipo_transacao = 'compra' THEN t.quantidade 
                                 WHEN t.tipo_transacao = 'venda' THEN -t.quantidade 
                                 ELSE 0 END) as total_cotas,
                        AVG(CASE WHEN t.tipo_transacao = 'compra' THEN t.valor_unitario ELSE NULL END) as valor_medio,
                        SUM(CASE WHEN t.tipo_transacao = 'compra' THEN t.valor_total 
                                 WHEN t.tipo_transacao = 'venda' THEN -t.valor_total 
                                 ELSE 0 END) as valor_investido
                    FROM Ativos a
                    LEFT JOIN InvestimentoTransacoes t ON a.ativo_id = t.ativo_id AND t.user_id = ?
                    GROUP BY a.ativo_id, a.asset_symbol, a.asset_name, a.asset_type
                    HAVING total_cotas > 0
                    ORDER BY a.asset_symbol";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$userId]);
            $carteira = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            echo json_encode([
                'sucesso' => true,
                'carteira' => $carteira
            ]);
            break;

        // Histórico de transações
        case 'historico':
            $sql = "SELECT 
                        t.transacao_id,
                        a.asset_symbol,
                        a.asset_name,
                        t.tipo_transacao,
                        t.quantidade,
                        t.valor_unitario,
                        t.valor_total,
                        t.data_transacao
                    FROM InvestimentoTransacoes t
                    JOIN Ativos a ON t.ativo_id = a.ativo_id
                    WHERE t.user_id = ?
                    ORDER BY t.data_transacao DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$userId]);
            $transacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            echo json_encode([
                'sucesso' => true,
                'transacoes' => $transacoes
            ]);
            break;

        // Estatísticas
        case 'estatisticas':
            $sql = "SELECT 
                        SUM(CASE WHEN t.tipo_transacao = 'compra' THEN t.valor_total 
                                 WHEN t.tipo_transacao = 'venda' THEN -t.valor_total 
                                 ELSE 0 END) as patrimonio_total,
                        COUNT(DISTINCT CASE WHEN SUM(CASE WHEN t.tipo_transacao = 'compra' THEN t.quantidade 
                                                            WHEN t.tipo_transacao = 'venda' THEN -t.quantidade 
                                                            ELSE 0 END) > 0 THEN t.ativo_id END) as qtd_ativos
                    FROM InvestimentoTransacoes t
                    WHERE t.user_id = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$userId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            echo json_encode([
                'sucesso' => true,
                'estatisticas' => $stats ?: ['patrimonio_total' => 0, 'qtd_ativos' => 0]
            ]);
            break;

        // Registrar novo ativo (útil para debug/teste)
        case 'registrar_ativo':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['erro' => 'Método não permitido']);
                break;
            }

            $dados = json_decode(file_get_contents('php://input'), true);

            if (!$dados) {
                http_response_code(400);
                echo json_encode(['erro' => 'Dados inválidos']);
                break;
            }

            $symbol = $dados['asset_symbol'] ?? null;
            $nome = $dados['asset_name'] ?? null;
            $tipo = $dados['asset_type'] ?? 'Ação';
            $setor = $dados['asset_sector'] ?? 'Outro';

            if (!$symbol || !$nome) {
                http_response_code(400);
                echo json_encode(['erro' => 'Symbol e Nome são obrigatórios']);
                break;
            }

            // Verificar se já existe
            $stmt = $db->prepare("SELECT ativo_id FROM Ativos WHERE asset_symbol = ?");
            $stmt->execute([$symbol]);
            $existe = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existe) {
                http_response_code(400);
                echo json_encode(['erro' => 'Ativo já existe com este symbol']);
                break;
            }

            // Registrar
            $sqlInsert = "INSERT INTO Ativos (asset_symbol, asset_name, asset_type, asset_sector) 
                         VALUES (?, ?, ?, ?)";
            
            $stmt = $db->prepare($sqlInsert);
            $result = $stmt->execute([$symbol, $nome, $tipo, $setor]);

            if ($result) {
                http_response_code(201);
                echo json_encode([
                    'sucesso' => true,
                    'mensagem' => 'Ativo registrado com sucesso!',
                    'ativoId' => $database->getPDO()->lastInsertId()
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['erro' => 'Erro ao registrar ativo', 'sql_error' => $stmt->errorInfo()]);
            }
            break;

        case 'comprar':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['erro' => 'Método não permitido']);
                break;
            }

            $dados = json_decode(file_get_contents('php://input'), true);

            if (!$dados) {
                http_response_code(400);
                echo json_encode(['erro' => 'Dados inválidos', 'debug' => file_get_contents('php://input')]);
                break;
            }

            $assetSymbol = $dados['asset_symbol'] ?? null;
            $assetName = $dados['asset_name'] ?? null;
            $quantidade = (int)($dados['quantidade'] ?? 0);
            $valorUnitario = (float)($dados['valor_unitario'] ?? 0);

            // Debug
            error_log("Compra recebida - Symbol: $assetSymbol, Name: $assetName, Qtd: $quantidade, Valor: $valorUnitario");

            if (!$assetSymbol || $quantidade <= 0 || $valorUnitario <= 0) {
                http_response_code(400);
                echo json_encode(['erro' => 'Parâmetros inválidos', 'dados_recebidos' => $dados]);
                break;
            }

            // Buscar o ativo_id pelo symbol
            $stmt = $db->prepare("SELECT ativo_id FROM Ativos WHERE asset_symbol = ?");
            $stmt->execute([$assetSymbol]);
            $ativo = $stmt->fetch(PDO::FETCH_ASSOC);

            $ativoId = null;

            // Se não existe, REGISTRAR NO BANCO AUTOMATICAMENTE
            if (!$ativo) {
                error_log("Ativo não encontrado ($assetSymbol). Criando automaticamente...");
                
                // Usar nome fornecido ou usar o symbol como fallback
                $nomeFinal = $assetName ?: $assetSymbol;
                
                $sqlInsert = "INSERT INTO Ativos (asset_symbol, asset_name, asset_type, asset_sector) 
                             VALUES (?, ?, 'Ação', 'Outro')";
                
                $stmtInsert = $db->prepare($sqlInsert);
                $resultInsert = $stmtInsert->execute([$assetSymbol, $nomeFinal]);
                
                if ($resultInsert) {
                    $ativoId = $database->getPDO()->lastInsertId();
                    error_log("Ativo criado com sucesso: ID=$ativoId, Symbol=$assetSymbol");
                } else {
                    http_response_code(500);
                    echo json_encode(['erro' => 'Erro ao criar novo ativo', 'sql_error' => $stmtInsert->errorInfo()]);
                    break;
                }
            } else {
                $ativoId = $ativo['ativo_id'];
            }

            $valorTotal = $quantidade * $valorUnitario;

            error_log("Salvando transação: AtivoID=$ativoId, UserID=$userId, Qtd=$quantidade, ValorUnitario=$valorUnitario");

            // Inserir transação no banco
            $sql = "INSERT INTO InvestimentoTransacoes 
                    (user_id, ativo_id, quantidade, valor_unitario, valor_total, tipo_transacao, data_transacao) 
                    VALUES (?, ?, ?, ?, ?, 'compra', NOW())";
            
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([$userId, $ativoId, $quantidade, $valorUnitario, $valorTotal]);

            if ($result) {
                http_response_code(201);
                echo json_encode([
                    'sucesso' => true,
                    'mensagem' => 'Investimento adicionado com sucesso!',
                    'transacaoId' => $database->getPDO()->lastInsertId(),
                    'ativoId' => $ativoId
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['erro' => 'Erro ao salvar investimento', 'sql_error' => $stmt->errorInfo()]);
            }
            break;

        // Vender
        case 'vender':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['erro' => 'Método não permitido']);
                break;
            }

            $dados = json_decode(file_get_contents('php://input'), true);

            if (!$dados) {
                http_response_code(400);
                echo json_encode(['erro' => 'Dados inválidos']);
                break;
            }

            $ativoId = (int)($dados['ativo_id'] ?? 0);
            $quantidade = (int)($dados['quantidade'] ?? 0);
            $valorUnitario = (float)($dados['valor_unitario'] ?? 0);

            if ($ativoId <= 0 || $quantidade <= 0 || $valorUnitario <= 0) {
                http_response_code(400);
                echo json_encode(['erro' => 'Parâmetros inválidos']);
                break;
            }

            $valorTotal = $quantidade * $valorUnitario;

            // Inserir transação de venda
            $sql = "INSERT INTO InvestimentoTransacoes 
                    (user_id, ativo_id, quantidade, valor_unitario, valor_total, tipo_transacao, data_transacao) 
                    VALUES (?, ?, ?, ?, ?, 'venda', NOW())";
            
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([$userId, $ativoId, $quantidade, $valorUnitario, $valorTotal]);

            if ($result) {
                http_response_code(200);
                echo json_encode([
                    'sucesso' => true,
                    'mensagem' => 'Venda realizada com sucesso!',
                    'transacaoId' => $database->getPDO()->lastInsertId()
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['erro' => 'Erro ao processar venda']);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['erro' => 'Ação não reconhecida']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no servidor: ' . $e->getMessage()]);
}
?>
