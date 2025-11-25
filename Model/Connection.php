<?php
namespace Model;

use PDO;
use PDOException;

// Tenta encontrar o arquivo de configuração automaticamente
if (file_exists(__DIR__ . "/../configuration.php")) {
    require_once __DIR__ . "/../configuration.php"; // Raiz
} elseif (file_exists(__DIR__ . "/../Config/configuration.php")) {
    require_once __DIR__ . "/../Config/configuration.php"; // Pasta Config
} else {
    die("Erro Crítico: O arquivo configuration.php não foi encontrado.");
}

class Connection
{
    private static $stmt;

    public static function getInstance(): PDO
    {
        if (empty(self::$stmt)) {
            try {
                // Cria a conexão usando as constantes do configuration.php
                self::$stmt = new PDO(
                    'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME, 
                    DB_USER, 
                    DB_PASSWORD
                );
                
                // Garante que caracteres especiais (acentos) funcionem bem
                self::$stmt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$stmt->exec("SET NAMES utf8");
                
            } catch (PDOException $error) {
                die("Erro de conexão com o banco de dados: " . $error->getMessage());
            }
        }
        return self::$stmt;
    }
}
?>