<?php
/**
 * Teste de API - Registrar e Comprar Ações
 */

echo "<h1>Testes da API de Investimentos</h1>";

// URL base da API
$baseUrl = 'http://localhost/Gerenciador-Financeiro-1/View/api_investimento.php';

// =============== TESTE 1: Carregar Carteira ===============
echo "<h2>1. Carteira Atual</h2>";
$url = $baseUrl . '?acao=carteira';
$response = file_get_contents($url);
$data = json_decode($response, true);
echo "<pre>";
print_r($data);
echo "</pre>";

// =============== TESTE 2: Registrar um novo ativo ===============
echo "<h2>2. Registrar novo ativo (TSLA34 - Tesla)</h2>";
$novoAtivo = [
    'asset_symbol' => 'TSLA34',
    'asset_name' => 'Tesla Inc.',
    'asset_type' => 'Ação',
    'asset_sector' => 'Tecnologia'
];

$ch = curl_init($baseUrl . '?acao=registrar_ativo');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($novoAtivo));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
echo "<pre>";
print_r($data);
echo "</pre>";

// =============== TESTE 3: Comprar um ativo (pode ser novo) ===============
echo "<h2>3. Fazer uma compra de TSLA34</h2>";
$compra = [
    'asset_symbol' => 'TSLA34',
    'asset_name' => 'Tesla Inc.',
    'quantidade' => 10,
    'valor_unitario' => 250.50
];

$ch = curl_init($baseUrl . '?acao=comprar');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($compra));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
echo "<pre>";
print_r($data);
echo "</pre>";

// =============== TESTE 4: Carteira após a compra ===============
echo "<h2>4. Carteira após a compra</h2>";
$url = $baseUrl . '?acao=carteira';
$response = file_get_contents($url);
$data = json_decode($response, true);
echo "<pre>";
print_r($data);
echo "</pre>";

echo "<h2>✅ Testes concluídos!</h2>";
?>