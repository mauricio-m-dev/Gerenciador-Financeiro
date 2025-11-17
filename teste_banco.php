<?php
/**
 * teste_banco.php
 * Arquivo para testar a conexão e operações do banco de dados
 */

require_once __DIR__ . '/app/init.php';

echo "<h1>Teste de Integração MVC</h1>";

try {
    // Teste 1: Conexão
    echo "<h2>✓ Conexão com banco de dados OK</h2>";

    // Teste 2: Listar todos os ativos
    echo "<h3>Ativos no banco:</h3>";
    $ativos = $ativoModel->listarTodos();
    
    if (empty($ativos)) {
        echo "<p>Nenhum ativo cadastrado ainda.</p>";
    } else {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Symbol</th><th>Name</th><th>Type</th></tr>";
        foreach ($ativos as $ativo) {
            echo "<tr>";
            echo "<td>{$ativo['ativo_id']}</td>";
            echo "<td>{$ativo['asset_symbol']}</td>";
            echo "<td>{$ativo['asset_name']}</td>";
            echo "<td>{$ativo['asset_type']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Teste 3: Carteira do usuário 1
    echo "<h3>Carteira do usuário 1:</h3>";
    $carteira = $investimentoController->obterCarteiraUsuario(1);
    
    if (empty($carteira)) {
        echo "<p>Nenhum investimento na carteira do usuário 1.</p>";
    } else {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Ativo</th><th>Símbolo</th><th>Cotas</th><th>Valor Médio</th><th>Valor Total</th></tr>";
        foreach ($carteira as $ativo) {
            echo "<tr>";
            echo "<td>{$ativo['asset_name']}</td>";
            echo "<td>{$ativo['asset_symbol']}</td>";
            echo "<td>{$ativo['total_cotas']}</td>";
            echo "<td>R$ " . number_format($ativo['valor_medio'], 2, ',', '.') . "</td>";
            echo "<td>R$ " . number_format($ativo['valor_investido'], 2, ',', '.') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Teste 4: Estatísticas
    echo "<h3>Estatísticas do usuário 1:</h3>";
    $stats = $investimentoController->calcularEstatisticas(1);
    echo "<p><strong>Patrimônio Total:</strong> R$ " . number_format($stats['patrimonio_total'], 2, ',', '.') . "</p>";
    echo "<p><strong>Quantidade de Ativos:</strong> " . $stats['qtd_ativos'] . "</p>";

    // Teste 5: Histórico de transações
    echo "<h3>Histórico de transações do usuário 1:</h3>";
    $historico = $investimentoController->obterHistoricoTransacoes(1);
    
    if (empty($historico)) {
        echo "<p>Nenhuma transação encontrada.</p>";
    } else {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Data</th><th>Ativo</th><th>Tipo</th><th>Quantidade</th><th>Valor Unit.</th><th>Total</th></tr>";
        foreach ($historico as $tx) {
            $tipo = strtoupper($tx['tipo_transacao']);
            echo "<tr>";
            echo "<td>{$tx['data_transacao']}</td>";
            echo "<td>{$tx['asset_name']} ({$tx['asset_symbol']})</td>";
            echo "<td>{$tipo}</td>";
            echo "<td>{$tx['quantidade']}</td>";
            echo "<td>R$ " . number_format($tx['valor_unitario'], 2, ',', '.') . "</td>";
            echo "<td>R$ " . number_format($tx['valor_total'], 2, ',', '.') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    echo "<hr>";
    echo "<h3>✓ Todos os testes passaram com sucesso!</h3>";
    echo "<p><a href='View/Investimento.php'>Voltar para a página de investimentos</a></p>";

} catch (Exception $e) {
    echo "<h2 style='color: red;'>✗ Erro: " . $e->getMessage() . "</h2>";
}
?>
