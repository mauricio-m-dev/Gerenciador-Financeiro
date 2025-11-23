<?php
/**
 * Teste de conexão com o banco
 */

require_once 'config/Database.php';

try {
    $db = new Database();
    $pdo = $db->connect();
    
    echo "<h2>✅ Conexão com o banco bem-sucedida!</h2>";
    
    // Listar ativos
    $stmt = $pdo->query("SELECT * FROM Ativos LIMIT 5");
    $ativos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Ativos (primeiros 5):</h3>";
    echo "<pre>";
    print_r($ativos);
    echo "</pre>";
    
    // Listar transações
    $stmt = $pdo->query("SELECT * FROM InvestimentoTransacoes LIMIT 5");
    $transacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Transações (primeiras 5):</h3>";
    echo "<pre>";
    print_r($transacoes);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h2>❌ Erro:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>