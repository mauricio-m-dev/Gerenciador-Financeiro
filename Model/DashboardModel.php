<?php
/*
|--------------------------------------------------------------------------
| Model (DashboardModel) - Sem Foto
|--------------------------------------------------------------------------
*/

class DashboardModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // REMOVI profile_pic_url DESTA CONSULTA
    public function getUserDetails($userId)
    {
        $stmt = $this->pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getGlobalTotals($userId)
    {
        $sql = "SELECT tipo, SUM(quantia) as total 
                FROM transacoes 
                WHERE usuario_id = ? 
                GROUP BY tipo";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return [
            'totalRenda' => $results['renda'] ?? 0,
            'totalDespesas' => abs($results['despesa'] ?? 0)
        ];
    }

    public function getTotalMetas($userId)
    {
        $stmt = $this->pdo->prepare("SELECT SUM(valor_atual) as total FROM metas WHERE usuario_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: 0;
    }

    public function getRecentTransactions($userId, $limit = 5)
    {
        $sql = "SELECT t.id, t.nome, t.metodo_pagamento as metodo, t.data_transacao as data, t.quantia, t.tipo, c.nome as categoria_nome
                FROM transacoes t
                LEFT JOIN categorias c ON t.categoria_id = c.id
                WHERE t.usuario_id = ?
                ORDER BY t.data_transacao DESC
                LIMIT $limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addTransaction($data)
    {
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

    public function deleteTransaction($transactionId, $userId)
    {
        $sql = "DELETE FROM transacoes WHERE id = ? AND usuario_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$transactionId, $userId]);
        return $stmt->rowCount() > 0;
    }

    public function getAllTransactions($userId)
    {
        return $this->getRecentTransactions($userId, 999999);
    }

    // Buscar Categorias (AGORA GLOBAIS)
    public function getCategorias($userId, $tipo)
    {
        // O $userId ainda é recebido para manter compatibilidade com o Controller,
        // mas NÃO o usamos mais na consulta SQL abaixo.

        $sql = "SELECT id, nome FROM categorias 
                WHERE tipo = ? 
                ORDER BY nome ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tipo]); // Passa apenas o tipo

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getCategoryExpenses($userId)
    {
        $sql = "SELECT c.nome, ABS(SUM(t.quantia)) as total 
                FROM transacoes t
                JOIN categorias c ON t.categoria_id = c.id
                WHERE t.usuario_id = ? AND t.tipo = 'despesa'
                GROUP BY c.nome
                ORDER BY total DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $valores = [];
        foreach ($data as $row) {
            $labels[] = $row['nome'];
            $valores[] = $row['total'];
        }
        return ['labels' => $labels, 'valores' => $valores];
    }
}
?>