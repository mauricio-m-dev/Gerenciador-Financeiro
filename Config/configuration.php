<?php
/*
|--------------------------------------------------------------------------
| Conexão com Banco de Dados (PDO) - Versão Final Integrada
|--------------------------------------------------------------------------
*/

// 1. Definição das Constantes (Protegidas com !defined para evitar erro de redefinição)
if (!defined("DB_HOST"))     define("DB_HOST", "localhost");
if (!defined("DB_NAME"))     define("DB_NAME", "financeiro");
if (!defined("DB_USER"))     define("DB_USER", "root");
if (!defined("DB_PASSWORD")) define("DB_PASSWORD", "");
if (!defined("DB_PORT"))     define("DB_PORT", "3306");
if (!defined("DB_CHARSET"))  define("DB_CHARSET", "utf8mb4");

// 2. A Função que o Meta.php está procurando
function getConexao() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
        return $pdo;

    } catch (PDOException $e) {
        // Retorna JSON se der erro, pois o JS espera uma resposta limpa
        // Se for acesso direto, o usuário verá o JSON, o que é seguro o suficiente para debug
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error', 
            'message' => 'Erro de conexão com o banco: ' . $e->getMessage()
        ]);
        exit;
    }
}

// 3. (Opcional) Se algum arquivo antigo seu usa $pdo diretamente fora da função,
// descomente a linha abaixo. Mas para o Meta.php, apenas a função acima basta.
// $pdo = getConexao();
?>