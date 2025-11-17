<?php
/**
 * Model: Ativo
 * Gerencia operações com a tabela 'Ativos'
 */
class Ativo
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Busca um ativo pelo símbolo (ticker)
     * @param string $symbol Ex: "PETR4"
     * @return array|null Dados do ativo ou null se não encontrado
     */
    public function buscarPorSymbol($symbol)
    {
        $sql = "SELECT * FROM Ativos WHERE asset_symbol = :symbol";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':symbol', $symbol, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Busca um ativo pelo ID
     * @param int $ativoId
     * @return array|null
     */
    public function buscarPorId($ativoId)
    {
        $sql = "SELECT * FROM Ativos WHERE ativo_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $ativoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lista todos os ativos
     * @return array
     */
    public function listarTodos()
    {
        $sql = "SELECT * FROM Ativos ORDER BY asset_name";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Cria um novo ativo
     * @param array $dados Contém: asset_symbol, asset_name, asset_type, asset_sector
     * @return int ID do ativo criado ou false se falhar
     */
    public function criar($dados)
    {
        $sql = "INSERT INTO Ativos (asset_symbol, asset_name, asset_type, asset_sector)
                VALUES (:symbol, :name, :type, :sector)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':symbol', $dados['asset_symbol'], PDO::PARAM_STR);
        $stmt->bindParam(':name', $dados['asset_name'], PDO::PARAM_STR);
        $stmt->bindParam(':type', $dados['asset_type'], PDO::PARAM_STR);
        $stmt->bindParam(':sector', $dados['asset_sector'], PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Atualiza um ativo
     * @param int $ativoId
     * @param array $dados
     * @return bool
     */
    public function atualizar($ativoId, $dados)
    {
        $sql = "UPDATE Ativos SET 
                asset_symbol = :symbol,
                asset_name = :name,
                asset_type = :type,
                asset_sector = :sector
                WHERE ativo_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $ativoId, PDO::PARAM_INT);
        $stmt->bindParam(':symbol', $dados['asset_symbol'], PDO::PARAM_STR);
        $stmt->bindParam(':name', $dados['asset_name'], PDO::PARAM_STR);
        $stmt->bindParam(':type', $dados['asset_type'], PDO::PARAM_STR);
        $stmt->bindParam(':sector', $dados['asset_sector'], PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    /**
     * Deleta um ativo
     * @param int $ativoId
     * @return bool
     */
    public function deletar($ativoId)
    {
        $sql = "DELETE FROM Ativos WHERE ativo_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $ativoId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
