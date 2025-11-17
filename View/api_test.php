<?php
/**
 * api_test.php - Teste simples da API
 * Coloque este arquivo em: View/api_test.php
 */

echo "Teste de Path<br>";
echo "Current Directory: " . __DIR__ . "<br>";
echo "Parent Directory: " . dirname(__DIR__) . "<br>";

$mockPath = dirname(__DIR__) . '/api/investimento_mock.php';
echo "Mock Path: " . $mockPath . "<br>";
echo "File Exists: " . (file_exists($mockPath) ? "SIM" : "NÃO") . "<br>";

if (file_exists($mockPath)) {
    echo "<br>Carregando mock...<br>";
    require_once $mockPath;
    echo "✅ Mock carregado com sucesso!";
} else {
    echo "❌ Arquivo não encontrado!";
}
?>
