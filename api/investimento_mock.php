<?php
/**
 * api_investimento_mock.php
 * API temporária com dados mockados para testes
 * Substitua pela api/investimento.php quando o banco estiver configurado
 */

header('Content-Type: application/json; charset=utf-8');

$acao = $_GET['acao'] ?? null;
$userId = $_GET['user_id'] ?? 1;

try {
    switch ($acao) {
        // Carteira de teste
        case 'carteira':
            $carteira = [
                [
                    'ativo_id' => 1,
                    'asset_symbol' => 'PETR4',
                    'asset_name' => 'Petrobras PN',
                    'asset_type' => 'Ação',
                    'total_cotas' => 45,
                    'valor_medio' => 30.50,
                    'valor_investido' => 1372.50
                ],
                [
                    'ativo_id' => 2,
                    'asset_symbol' => 'VALE3',
                    'asset_name' => 'Vale ON',
                    'asset_type' => 'Ação',
                    'total_cotas' => 30,
                    'valor_medio' => 85.20,
                    'valor_investido' => 2556.00
                ],
                [
                    'ativo_id' => 4,
                    'asset_symbol' => 'ABEV3',
                    'asset_name' => 'Ambev ON',
                    'asset_type' => 'Ação',
                    'total_cotas' => 50,
                    'valor_medio' => 12.80,
                    'valor_investido' => 640.00
                ]
            ];
            
            http_response_code(200);
            echo json_encode([
                'sucesso' => true,
                'carteira' => $carteira
            ]);
            break;

        // Histórico de transações
        case 'historico':
            $historico = [
                [
                    'transacao_id' => 1,
                    'asset_symbol' => 'PETR4',
                    'asset_name' => 'Petrobras PN',
                    'tipo_transacao' => 'compra',
                    'quantidade' => 45,
                    'valor_unitario' => 30.50,
                    'valor_total' => 1372.50,
                    'data_transacao' => '2025-10-21 10:00:00'
                ],
                [
                    'transacao_id' => 2,
                    'asset_symbol' => 'VALE3',
                    'asset_name' => 'Vale ON',
                    'tipo_transacao' => 'compra',
                    'quantidade' => 30,
                    'valor_unitario' => 85.20,
                    'valor_total' => 2556.00,
                    'data_transacao' => '2025-10-20 14:30:00'
                ]
            ];
            
            http_response_code(200);
            echo json_encode([
                'sucesso' => true,
                'transacoes' => $historico
            ]);
            break;

        // Estatísticas
        case 'estatisticas':
            http_response_code(200);
            echo json_encode([
                'sucesso' => true,
                'estatisticas' => [
                    'patrimonio_total' => 4568.50,
                    'qtd_ativos' => 3,
                    'carteira' => [
                        ['asset_name' => 'Petrobras PN', 'valor' => 1372.50],
                        ['asset_name' => 'Vale ON', 'valor' => 2556.00],
                        ['asset_name' => 'Ambev ON', 'valor' => 640.00]
                    ]
                ]
            ]);
            break;

        // Comprar (retorna sucesso)
        case 'comprar':
            http_response_code(201);
            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Investimento adicionado com sucesso!',
                'transacaoId' => 999
            ]);
            break;

        // Vender (retorna sucesso)
        case 'vender':
            http_response_code(200);
            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Venda realizada com sucesso!',
                'transacaoId' => 1000
            ]);
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
