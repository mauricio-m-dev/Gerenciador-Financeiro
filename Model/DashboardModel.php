<?php
/*
|--------------------------------------------------------------------------
| Model (DashboardModel) 
|--------------------------------------------------------------------------
*/
class DashboardModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getUserDetails($userId) {
        $stmt = $this->pdo->prepare("SELECT nome, profile_pic_url FROM usuarios WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getGlobalTotals($userId) {
        $sql = "SELECT tipo, SUM(quantia) as total 
                FROM transacoes 
                WHERE usuario_id = ? AND (tipo = 'renda' OR tipo = 'despesa')
                GROUP BY tipo";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return [
            'totalRenda' => $results['renda'] ?? 0,
            'totalDespesas' => abs($results['despesa'] ?? 0)
        ];
    }

    public function getTotalMetas($userId) {
        $stmt = $this->pdo->prepare("SELECT SUM(valor_atual) as total FROM metas WHERE usuario_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: 0;
    }

    // Traz o ID para permitir exclusão
    public function getRecentTransactions($userId, $limit = 5) {
        $stmt = $this->pdo->prepare(
            "SELECT id, nome, metodo_pagamento as metodo, data_transacao as data, quantia, tipo 
             FROM transacoes 
             WHERE usuario_id = ?
             ORDER BY data_transacao DESC LIMIT ?"
        );
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryExpenses($userId) {
        $sql = "SELECT c.nome, SUM(t.quantia) as total 
                FROM transacoes t
                JOIN categorias c ON t.categoria_id = c.id
                WHERE t.usuario_id = ? AND t.tipo = 'despesa'
                GROUP BY c.id, c.nome
                ORDER BY total ASC"; 
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'labels' => array_column($data, 'nome'),
            'valores' => array_column($data, 'total')
        ];
    }

    public function getCategorias($userId, $tipo) {
        $stmt = $this->pdo->prepare("SELECT id, nome FROM categorias WHERE usuario_id = ? AND tipo = ? ORDER BY nome ASC");
        $stmt->execute([$userId, $tipo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addTransaction($data) {
        $sql = "INSERT INTO transacoes (usuario_id, categoria_id, nome, quantia, tipo, data_transacao, metodo_pagamento) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['usuario_id'],
            $data['categoria_id'],
            $data['nome'],
            $data['quantia'], 
            $data['tipo'],
            $data['data'],
            $data['metodo']
        ]);
        return $stmt->rowCount() > 0;
    }

    // Traz o ID e remove limite para o Extrato
    public function getAllTransactions($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT id, nome, metodo_pagamento as metodo, data_transacao as data, quantia, tipo 
             FROM transacoes 
             WHERE usuario_id = ?
             ORDER BY data_transacao DESC"
        );
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Função para deletar transação
    public function deleteTransaction($transactionId, $userId) {
        // Verifica usuario_id para segurança
        $sql = "DELETE FROM transacoes WHERE id = ? AND usuario_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$transactionId, $userId]);
        return $stmt->rowCount() > 0;
    }
}
?>