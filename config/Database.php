<?php
/**
 * Classe de Conexão com o Banco de Dados
 * Gerencia a conexão PDO com o MySQL
 */
class Database
{
    // Configurações do banco de dados
    private const DB_HOST = 'localhost';
    private const DB_NAME = 'gerenciador_financeiro_investimento'; 
    private const DB_USER = 'root';
    private const DB_PASS = '';
    
    private $pdo;

    /**
     * Conecta ao banco de dados
     * @return PDO Instância de conexão
     */
    public function connect()
    {
        if ($this->pdo === null) {
            try {
                $dsn = 'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4';
                $this->pdo = new PDO($dsn, self::DB_USER, self::DB_PASS);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
            }
        }
        return $this->pdo;
    }

    /**
     * Retorna a conexão PDO
     * @return PDO
     */
    public function getPDO()
    {
        return $this->connect();
    }
}
?>
