<?php
/**
 * debug.php
 * Arquivo de debug para testar a API
 */

// Teste 1: Verificar paths
echo "<h1>Debug - Verificação de Paths</h1>";

$currentDir = __DIR__;
$parentDir = dirname(__DIR__);
$appPath = $parentDir . '/app/init.php';

echo "<p><strong>Current Directory (__DIR__):</strong> " . htmlspecialchars($currentDir) . "</p>";
echo "<p><strong>Parent Directory:</strong> " . htmlspecialchars($parentDir) . "</p>";
echo "<p><strong>App Init Path:</strong> " . htmlspecialchars($appPath) . "</p>";
echo "<p><strong>File Exists:</strong> " . (file_exists($appPath) ? "✅ SIM" : "❌ NÃO") . "</p>";

// Teste 2: Tentar carregar init.php
echo "<h2>Teste 2 - Carregar init.php</h2>";
try {
    require_once $appPath;
    echo "<p>✅ init.php carregado com sucesso!</p>";
    echo "<p>✅ InvestimentoController disponível: " . (isset($investimentoController) ? "SIM" : "NÃO") . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Erro ao carregar: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Teste 3: Listar arquivos
echo "<h2>Teste 3 - Estrutura de Pastas</h2>";
echo "<p><strong>Arquivos em /app:</strong></p>";
$appDir = $parentDir . '/app';
if (is_dir($appDir)) {
    $files = scandir($appDir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "<li>" . htmlspecialchars($file) . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>❌ Pasta /app não encontrada!</p>";
}

// Teste 4: Testar chamada de API
echo "<h2>Teste 4 - Chamar API</h2>";
$url = "http://localhost/Gerenciador-Financeiro-1/api/investimento.php?acao=carteira";
echo "<p>URL: " . htmlspecialchars($url) . "</p>";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>Status HTTP:</strong> $http_code</p>";
echo "<p><strong>Resposta (primeiros 200 chars):</strong></p>";
echo "<pre>" . htmlspecialchars(substr($response, 0, 200)) . "</pre>";

if ($http_code === 200 && substr($response, 0, 1) === '{') {
    echo "<p>✅ API retornando JSON válido!</p>";
} else {
    echo "<p>❌ API retornando erro ou HTML</p>";
}
?>
