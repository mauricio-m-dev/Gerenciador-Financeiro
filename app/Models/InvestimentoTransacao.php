<?php
/**
 * Model: InvestimentoTransacao
 * Gerencia operações com a tabela 'InvestimentoTransacoes'
 */
class InvestimentoTransacao
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Busca uma transação pelo ID
     * @param int $transacaoId
     * @return array|null
     */
    public function buscarPorId($transacaoId)
    {
        $sql = "SELECT it.*, a.asset_symbol, a.asset_name
                FROM InvestimentoTransacoes it
                JOIN Ativos a ON it.ativo_id = a.ativo_id
                WHERE it.transacao_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $transacaoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lista todas as transações de um usuário
     * @param int $userId
     * @return array
     */
    public function listarPorUsuario($userId)
    {
        $sql = "SELECT it.*, a.asset_symbol, a.asset_name, a.asset_type
                FROM InvestimentoTransacoes it
                JOIN Ativos a ON it.ativo_id = a.ativo_id
                WHERE it.user_id = :userId
                ORDER BY it.data_transacao DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lista todas as transações
     * @return array
     */
    public function listarTodas()
    {
        $sql = "SELECT it.*, a.asset_symbol, a.asset_name
                FROM InvestimentoTransacoes it
                JOIN Ativos a ON it.ativo_id = a.ativo_id
                ORDER BY it.data_transacao DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Cria uma nova transação
     * @param array $dados
     * @return int|false ID da transação ou false se falhar
     */
    public function criar($dados)
    {
        $sql = "INSERT INTO InvestimentoTransacoes 
                (user_id, ativo_id, quantidade, valor_unitario, valor_total, tipo_transacao, data_transacao)
                VALUES (:userId, :ativoId, :quantidade, :valorUnitario, :valorTotal, :tipoTransacao, :dataTransacao)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':userId', $dados['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':ativoId', $dados['ativo_id'], PDO::PARAM_INT);
        $stmt->bindParam(':quantidade', $dados['quantidade'], PDO::PARAM_INT);
        $stmt->bindParam(':valorUnitario', $dados['valor_unitario'], PDO::PARAM_STR);
        $stmt->bindParam(':valorTotal', $dados['valor_total'], PDO::PARAM_STR);
        $stmt->bindParam(':tipoTransacao', $dados['tipo_transacao'], PDO::PARAM_STR);
        $stmt->bindParam(':dataTransacao', $dados['data_transacao'], PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Atualiza uma transação
     * @param int $transacaoId
     * @param array $dados
     * @return bool
     */
    public function atualizar($transacaoId, $dados)
    {
        $sql = "UPDATE InvestimentoTransacoes SET 
                quantidade = :quantidade,
                valor_unitario = :valorUnitario,
                valor_total = :valorTotal,
                tipo_transacao = :tipoTransacao,
                data_transacao = :dataTransacao
                WHERE transacao_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $transacaoId, PDO::PARAM_INT);
        $stmt->bindParam(':quantidade', $dados['quantidade'], PDO::PARAM_INT);
        $stmt->bindParam(':valorUnitario', $dados['valor_unitario'], PDO::PARAM_STR);
        $stmt->bindParam(':valorTotal', $dados['valor_total'], PDO::PARAM_STR);
        $stmt->bindParam(':tipoTransacao', $dados['tipo_transacao'], PDO::PARAM_STR);
        $stmt->bindParam(':dataTransacao', $dados['data_transacao'], PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    /**
     * Deleta uma transação
     * @param int $transacaoId
     * @return bool
     */
    public function deletar($transacaoId)
    {
        $sql = "DELETE FROM InvestimentoTransacoes WHERE transacao_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $transacaoId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Calcula o total de cotas por ativo para um usuário (saldo consolidado)
     * @param int $userId
     * @return array Contém ativo_id, asset_symbol, asset_name, total_cotas
     */
    public function obterSaldoCotas($userId)
    {
        $sql = "SELECT 
                a.ativo_id,
                a.asset_symbol,
                a.asset_name,
                a.asset_type,
                SUM(CASE WHEN it.tipo_transacao = 'compra' THEN it.quantidade ELSE -it.quantidade END) as total_cotas,
                AVG(CASE WHEN it.tipo_transacao = 'compra' THEN it.valor_unitario END) as valor_medio,
                SUM(CASE WHEN it.tipo_transacao = 'compra' THEN it.valor_total ELSE -it.valor_total END) as valor_investido
                FROM InvestimentoTransacoes it
                JOIN Ativos a ON it.ativo_id = a.ativo_id
                WHERE it.user_id = :userId
                GROUP BY a.ativo_id, a.asset_symbol, a.asset_name, a.asset_type
                HAVING total_cotas > 0
                ORDER BY a.asset_name";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
