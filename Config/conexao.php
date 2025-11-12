<?php
/*
|--------------------------------------------------------------------------
| Conexão com Banco de Dados (PDO) - Versão Corrigida (usando define)
|--------------------------------------------------------------------------
*/

// --- ETAPA 1: DEFINIR CONSTANTES ---
define("DB_NAME", "gerenciador_financeiro");
define("DB_HOST", "localhost"); // ou 127.0.0.1
define("DB_USER", "root");
define("DB_PASSWORD", ''); // Senha padrão do XAMPP é vazia
define("DB_PORT", "3306");
define("DB_CHARSET", "utf8mb4"); // <-- ADICIONADO (Estava faltando e causava erro)

// --- ETAPA 2: CONFIGURAÇÕES DO PDO ---
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// --- ETAPA 3: USAR AS CONSTANTES PARA CONECTAR ---
// CORRIGIDO: Agora usa as constantes (DB_HOST) em vez de variáveis ($db_host)
$dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

try {
    // CORRIGIDO: Agora usa as constantes
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);

} catch (\PDOException $e) {
    // Em produção, não mostre o erro detalhado.
    // Logue o erro e mostre uma mensagem genérica.
    error_log("Erro de conexão PDO: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Por favor, verifique a configuração.");
}
?>