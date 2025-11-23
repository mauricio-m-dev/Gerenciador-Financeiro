<?php
// CONFIGURAÇÕES DE USO

// EXEMPLO DE USO EM OUTRAS CLASSES = use Model\Connection
namespace Model;

// IMPORTAÇÃO PARA CONEXÃO COM BANCO DE DADOS
use PDO;
use PDOException;

// BUSCANDO DADOS DE CONFIGURAÇÃO DO BANCO DE DADOS
require_once __DIR__ . "/../Config/configuration.php";

class Connection
{
    // ATRIBUTO ESTÁTICO QUE IRÁ PERMITIR A CONEXÃO ABAIXO
    private static $pdo = null;

    // Construtor privado para evitar instanciação
    private function __construct()
    {
    }

    // Método para obter a instância única da conexão (Singleton Pattern)
    public static function getInstance(): PDO
    {
        if (self::$pdo === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
                
                self::$pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT => false,  // Evita conexões persistentes
                    PDO::ATTR_EMULATE_PREPARES => false,  // Usa prepared statements nativos
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"  // Define charset
                ]);
                
                // Definir timeout para a conexão (30 segundos)
                self::$pdo->setAttribute(PDO::ATTR_TIMEOUT, 30);
                
            } catch (PDOException $error) {
                // die("Erro de Conexão com o Banco de Dados: " . $error->getMessage());

                throw new PDOException("Erro de Conexão: " . $error->getMessage());
            }
        }
        return self::$pdo;
    }

    // Método para fechar a conexão (útil em scripts CLI)
    public static function closeConnection()
    {
        self::$pdo = null;
    }

    // Método para verificar se está conectado
    public static function isConnected(): bool
    {
        return self::$pdo !== null;
    }
}

?>