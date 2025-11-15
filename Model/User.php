<?php

class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Função auxiliar para preparar a cláusula WHERE
    private function getCardFilterSql($cardId) {
        return ($cardId) ? " AND id_cartao_fk = ? " : "";
    }

    /**
     * Busca todas as transações, filtradas por cartão se o ID for fornecido.
     * @param int|null $cardId
     * @param int $limit
     * @return array
     */
    public function getLatestTransactions($cardId = null, $limit = 10) {
        $filterSql = $this->getCardFilterSql($cardId);
        // Ajustando a cláusula WHERE/AND no SQL
        $sql = "SELECT descricao, metodo, data, quantia, categoria FROM transacoes WHERE 1=1 {$filterSql} ORDER BY data DESC, id DESC LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) { return []; }

        // Bind dos parâmetros
        if ($cardId) {
            $stmt->bind_param("ii", $cardId, $limit); // 'ii' para INT, INT (cardId e limit)
        } else {
            $stmt->bind_param("i", $limit); // 'i' para INT (limit)
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        $transacoes = [];
        while ($transacao = $result->fetch_assoc()) {
            $transacoes[] = $transacao;
        }

        $stmt->close();
        return $transacoes;
    }

    /**
     * Calcula o total de Renda (quantia > 0), filtrado por cartão.
     * @param int|null $cardId
     * @return float
     */
    public function calculateTotalIncome($cardId = null) {
        $filterSql = $this->getCardFilterSql($cardId);
        $sql = "SELECT SUM(quantia) AS total_renda FROM transacoes WHERE quantia > 0 {$filterSql}";
        
        $stmt = $this->conn->prepare($sql);
        if ($cardId) {
            $stmt->bind_param("i", $cardId);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return (float) $row['total_renda'];
    }

    /**
     * Calcula o total de Despesas (quantia < 0), filtrado por cartão.
     * @param int|null $cardId
     * @return float (valor absoluto)
     */
    public function calculateTotalExpenses($cardId = null) {
        $filterSql = $this->getCardFilterSql($cardId);
        $sql = "SELECT SUM(quantia) AS total_despesas FROM transacoes WHERE quantia < 0 {$filterSql}";
        
        $stmt = $this->conn->prepare($sql);
        if ($cardId) {
            $stmt->bind_param("i", $cardId);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return abs((float) $row['total_despesas']);
    }

    /**
     * Agrupa e soma as despesas por categoria, filtrado por cartão.
     * @param int|null $cardId
     * @return array
     */
    public function getExpensesByCategory($cardId = null) {
        $filterSql = $this->getCardFilterSql($cardId);
        // 1. Soma das despesas por categoria
        $sql = "SELECT categoria, SUM(quantia) AS valor FROM transacoes WHERE quantia < 0 {$filterSql} GROUP BY categoria ORDER BY valor ASC";
        
        $stmt = $this->conn->prepare($sql);
        if ($cardId) {
            $stmt->bind_param("i", $cardId);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $despesas_por_categoria = [];
        $soma_total_despesas = $this->calculateTotalExpenses($cardId); // Chama a função com o filtro

        while ($row = $result->fetch_assoc()) {
            $valor_abs = abs((float) $row['valor']);
            $porcentagem = ($soma_total_despesas > 0) ? ($valor_abs / $soma_total_despesas) * 100 : 0;
            
            $despesas_por_categoria[] = [
                'label' => $row['categoria'],
                'valor' => $valor_abs,
                'porcentagem' => round($porcentagem, 2)
            ];
        }
        $stmt->close();
        return $despesas_por_categoria;
    }
}