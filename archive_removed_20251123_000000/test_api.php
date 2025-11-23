<?php
/**
 * test_api.php
 * Teste rápido da API
 */

echo "<h1>Teste da API de Investimentos</h1>";

// Teste 1: Carregar carteira
echo "<h2>Teste 1: Carteira</h2>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/Gerenciador-Financeiro-1/api/investimento.php?acao=carteira");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>Status HTTP:</strong> $http_code</p>";
echo "<p><strong>Resposta:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Teste 2: Histórico
echo "<h2>Teste 2: Histórico</h2>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/Gerenciador-Financeiro-1/api/investimento.php?acao=historico");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>Status HTTP:</strong> $http_code</p>";
echo "<p><strong>Resposta (primeiros 500 chars):</strong></p>";
echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";

// Teste 3: Estatísticas
echo "<h2>Teste 3: Estatísticas</h2>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/Gerenciador-Financeiro-1/api/investimento.php?acao=estatisticas");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>Status HTTP:</strong> $http_code</p>";
echo "<p><strong>Resposta:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

echo "<h2>✅ Se tudo acima retornar JSON, a API está funcionando!</h2>";
echo "<p><a href='View/Investimento.php'>Voltar para a página</a></p>";
?>