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
        // CORREÇÃO: Nome da tabela em minúsculo
        $stmt = $this->pdo->prepare("SELECT nome, profile_pic_url FROM usuarios WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Busca os totais GLOBAIS (Renda e Despesa de todas as contas)
    // OTIMIZAÇÃO: Feita com 1 query (GROUP BY) em vez de 2
    public function getGlobalTotals($userId) {
        $sql = "SELECT 
                    tipo, 
                    SUM(quantia) as total 
                FROM 
                    transacoes -- Tabela em minúsculo
                WHERE 
                    usuario_id = ? AND (tipo = 'renda' OR tipo = 'despesa')
                GROUP BY 
                    tipo";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        // Cria um array associativo ex: ['renda' => 5000, 'despesa' => -1500]
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return [
            'totalRenda' => $results['renda'] ?? 0,
            'totalDespesas' => abs($results['despesa'] ?? 0) // Retorna o valor absoluto
        ];
    }

    // Busca o total de Metas (sempre foi global)
    public function getTotalMetas($userId) {
        // CORREÇÃO: Nome da tabela em minúsculo
        $stmt = $this->pdo->prepare("SELECT SUM(valor_atual) as total FROM metas WHERE usuario_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: 0;
    }

    // Busca transações recentes GLOBAIS
    public function getRecentTransactions($userId, $limit = 5) {
        // CORREÇÃO: Nome da tabela em minúsculo
        $stmt = $this->pdo->prepare(
            "SELECT nome, metodo_pagamento as metodo, data_transacao as data, quantia, tipo 
             FROM transacoes 
             WHERE usuario_id = ?
             ORDER BY data_transacao DESC LIMIT ?"
        );
        // Bind do limite como INT
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca despesas GLOBAIS por categoria (para o gráfico)
    public function getCategoryExpenses($userId) {
        // CORREÇÃO: Nomes das tabelas em minúsculo
        $sql = "SELECT c.nome, SUM(t.quantia) as total 
                FROM transacoes t
                JOIN categorias c ON t.categoria_id = c.id
                WHERE t.usuario_id = ? AND t.tipo = 'despesa'
                GROUP BY c.nome
                ORDER BY total ASC"; // Despesas são negativas, ASC = maior despesa
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formata para o Chart.js
        return [
            'labels' => array_column($data, 'nome'),
            'valores' => array_column($data, 'total') // View irá aplicar abs()
        ];
    }

    // Busca categorias (para o modal)
    public function getCategorias($userId, $tipo) {
        // CORREÇÃO: Nome da tabela em minúsculo
        $stmt = $this->pdo->prepare("SELECT id, nome FROM categorias WHERE usuario_id = ? AND tipo = ? ORDER BY nome ASC");
        $stmt->execute([$userId, $tipo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Adiciona uma transação (versão global, sem conta_id)
    public function addTransaction($data) {
        // CORREÇÃO: Nome da tabela em minúsculo
        $sql = "INSERT INTO transacoes (usuario_id, categoria_id, nome, quantia, tipo, data_transacao, metodo_pagamento) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['usuario_id'],
            $data['categoria_id'],
            $data['nome'],
            $data['quantia'], // Já deve vir negativo se for despesa
            $data['tipo'],
            $data['data'],
            $data['metodo']
        ]);
        return $stmt->rowCount() > 0;
    }

    public function getAllTransactions($userId) {
        // CORREÇÃO: Tabela em minúsculo.
        // Trazemos a data_transacao (como 'data') e o 'tipo' para a View
        $stmt = $this->pdo->prepare(
            "SELECT nome, metodo_pagamento as metodo, data_transacao as data, quantia, tipo 
             FROM transacoes 
             WHERE usuario_id = ?
             ORDER BY data_transacao DESC" // <-- Sem LIMIT
        );
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>