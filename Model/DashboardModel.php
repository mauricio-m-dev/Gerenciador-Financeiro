<?php
/*
|--------------------------------------------------------------------------
| Model (DashboardModel) - Versão Global
|--------------------------------------------------------------------------
| Busca dados GLOBAIS do usuário (somando todas as contas).
*/
class DashboardModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Busca dados do usuário (nome, foto)
    public function getUserDetails($userId) {
        $stmt = $this->pdo->prepare("SELECT nome, profile_pic_url FROM Usuarios WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    // Busca os totais GLOBAIS (Renda e Despesa de todas as contas)
    public function getGlobalTotals($userId) {
        $totals = ['totalRenda' => 0, 'totalDespesas' => 0];

        // Total Renda
        $stmtRenda = $this->pdo->prepare(
            "SELECT SUM(quantia) as total FROM Transacoes 
             WHERE usuario_id = ? AND tipo = 'renda'"
        );
        $stmtRenda->execute([$userId]);
        $totals['totalRenda'] = $stmtRenda->fetchColumn() ?: 0;

        // Total Despesas
        $stmtDespesa = $this->pdo->prepare(
            "SELECT SUM(quantia) as total FROM Transacoes 
             WHERE usuario_id = ? AND tipo = 'despesa'"
        );
        $stmtDespesa->execute([$userId]);
        $totals['totalDespesas'] = $stmtDespesa->fetchColumn() ?: 0;

        return $totals;
    }

    // Busca o total de Metas (sempre foi global)
    public function getTotalMetas($userId) {
        $stmt = $this->pdo->prepare("SELECT SUM(valor_atual) as total FROM Metas WHERE usuario_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: 0;
    }

    // Busca transações recentes GLOBAIS
    public function getRecentTransactions($userId, $limit = 5) {
        $stmt = $this->pdo->prepare(
            "SELECT nome, metodo_pagamento as metodo, data_transacao as data, quantia, tipo 
             FROM Transacoes 
             WHERE usuario_id = ?
             ORDER BY data_transacao DESC LIMIT ?"
        );
        // Bind do limite como INT
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Busca despesas GLOBAIS por categoria (para o gráfico)
    public function getCategoryExpenses($userId) {
        $sql = "SELECT c.nome, SUM(t.quantia) as total 
                FROM Transacoes t
                JOIN Categorias c ON t.categoria_id = c.id
                WHERE t.usuario_id = ? AND t.tipo = 'despesa'
                GROUP BY c.nome
                ORDER BY total DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        $data = $stmt->fetchAll();
        
        // Formata para o Chart.js
        return [
            'labels' => array_column($data, 'nome'),
            'valores' => array_column($data, 'total')
        ];
    }

    // Busca categorias (para o modal)
    public function getCategorias($userId, $tipo) {
        $stmt = $this->pdo->prepare("SELECT id, nome FROM Categorias WHERE usuario_id = ? AND tipo = ? ORDER BY nome ASC");
        $stmt->execute([$userId, $tipo]);
        return $stmt->fetchAll();
    }

    // Adiciona uma transação (versão global, sem conta_id)
    public function addTransaction($data) {
        // O campo 'conta_id' foi removido do INSERT (será NULL)
        $sql = "INSERT INTO Transacoes (usuario_id, categoria_id, nome, quantia, tipo, data_transacao, metodo_pagamento) 
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
}
?>