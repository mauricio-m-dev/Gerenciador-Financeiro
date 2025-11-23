<?php
/**
 * api_investimento.php
 * API para gerenciar investimentos
 * Recebe requisições AJAX do frontend
 */

header('Content-Type: application/json; charset=utf-8');

// Debug: Verificar se init.php existe
$initPath = dirname(__DIR__) . '/app/init.php';

if (!file_exists($initPath)) {
    http_response_code(500);
    die(json_encode(['erro' => 'Arquivo init.php não encontrado em: ' . $initPath]));
}

try {
    require_once $initPath;
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode(['erro' => 'Erro ao carregar init.php: ' . $e->getMessage()]));
}

// Verifica se as variáveis globais foram criadas
if (!isset($investimentoController)) {
    http_response_code(500);
    die(json_encode(['erro' => 'InvestimentoController não inicializado']));
}

// Verifica o método da requisição
$metodo = $_SERVER['REQUEST_METHOD'];
$acao = $_GET['acao'] ?? null;

// IMPORTANTE: Substitua '1' pelo ID do usuário logado
// Você precisará integrar com seu sistema de autenticação
$userId = $_GET['user_id'] ?? 1; // Temporariamente usando user_id = 1

try {
    switch ($acao) {
        // ========== COMPRAR INVESTIMENTO ==========
        case 'comprar':
            if ($metodo !== 'POST') {
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

            $assetSymbol = $dados['asset_symbol'] ?? null;
            $quantidade = (int)($dados['quantidade'] ?? 0);
            $valorUnitario = (float)($dados['valor_unitario'] ?? 0);

            if (!$assetSymbol || $quantidade <= 0 || $valorUnitario <= 0) {
                http_response_code(400);
                echo json_encode(['erro' => 'Parâmetros inválidos']);
                break;
            }

            $resultado = $investimentoController->adicionarInvestimento(
                $userId,
                $assetSymbol,
                $quantidade,
                $valorUnitario
            );

            http_response_code($resultado['sucesso'] ? 201 : 400);
            echo json_encode($resultado);
            break;

        // ========== VENDER INVESTIMENTO ==========
        case 'vender':
            if ($metodo !== 'POST') {
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

            $resultado = $investimentoController->venderInvestimento(
                $userId,
                $ativoId,
                $quantidade,
                $valorUnitario
            );

            http_response_code($resultado['sucesso'] ? 200 : 400);
            echo json_encode($resultado);
            break;

        // ========== OBTER CARTEIRA ==========
        case 'carteira':
            $carteira = $investimentoController->obterCarteiraUsuario($userId);
            echo json_encode([
                'sucesso' => true,
                'carteira' => $carteira
            ]);
            break;

        // ========== OBTER HISTÓRICO ==========
        case 'historico':
            $historico = $investimentoController->obterHistoricoTransacoes($userId);
            echo json_encode([
                'sucesso' => true,
                'transacoes' => $historico
            ]);
            break;

        // ========== OBTER ESTATÍSTICAS ==========
        case 'estatisticas':
            $stats = $investimentoController->calcularEstatisticas($userId);
            echo json_encode([
                'sucesso' => true,
                'estatisticas' => $stats
            ]);
            break;

        // ========== APAGAR INVESTIMENTO ==========
        case 'apagar':
            if ($metodo !== 'DELETE') {
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

            $transacaoId = (int)($dados['transacao_id'] ?? 0);

            if ($transacaoId <= 0) {
                http_response_code(400);
                echo json_encode(['erro' => 'ID da transação inválido']);
                break;
            }

            $resultado = $investimentoController->apagarInvestimento($userId, $transacaoId);
            http_response_code($resultado['sucesso'] ? 200 : 400);
            echo json_encode($resultado);
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