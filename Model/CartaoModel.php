<?php
namespace Model;

use PDO;

class CartaoModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // 1. Busca todos os cartões
    public function getAllCards($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM cartoes WHERE usuario_id = ? ORDER BY id ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Resumo (CORRIGIDO PARA AS CHAVES CERTAS)
    public function getResumo($userId, $cardId) {
        // Soma Receitas e Despesas
        $sqlTrans = "SELECT 
                        SUM(CASE WHEN tipo = 'renda' THEN quantia ELSE 0 END) as total_renda,
                        SUM(CASE WHEN tipo = 'despesa' THEN quantia ELSE 0 END) as total_despesa
                    FROM transacoes 
                    WHERE usuario_id = ? AND cartao_id = ?";
        
        $stmt = $this->pdo->prepare($sqlTrans);
        $stmt->execute([$userId, $cardId]);
        $resTrans = $stmt->fetch(PDO::FETCH_ASSOC);

        // Soma Metas (Guardado)
        $sqlMetas = "SELECT SUM(valor_atual) as total_guardado 
                     FROM metas 
                     WHERE usuario_id = ? AND cartao_id = ?";
        
        $stmtMetas = $this->pdo->prepare($sqlMetas);
        $stmtMetas->execute([$userId, $cardId]);
        $resMetas = $stmtMetas->fetch(PDO::FETCH_ASSOC);

        $valorRenda   = $resTrans['total_renda'] ?? 0;
        $valorDespesa = $resTrans['total_despesa'] ?? 0;
        $valorMeta    = $resMetas['total_guardado'] ?? 0;

        // RETORNO COM AS CHAVES EXATAS QUE A VIEW PEDE
        return [
            'renda'   => (float)$valorRenda,
            'despesa' => (float)$valorDespesa, // View pede 'despesa' (singular)
            'meta'    => (float)$valorMeta,    // View pede 'meta'
            'saldo'   => (float)($valorRenda - $valorDespesa)
        ];
    }

    // 3. Lista de Transações
    public function getTransacoes($userId, $cardId) {
        $sql = "SELECT 
                    t.id, 
                    t.nome, 
                    t.quantia as valor, 
                    t.tipo, 
                    t.data_transacao as data, 
                    c.nome as categoria_nome
                FROM transacoes t
                LEFT JOIN categorias c ON t.categoria_id = c.id
                WHERE t.usuario_id = ? AND t.cartao_id = ? 
                ORDER BY t.data_transacao DESC 
                LIMIT 10";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $cardId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Gráfico
    public function getExpensesByCategory($userId, $cardId) {
        $sql = "SELECT 
                    c.nome as categoria, 
                    SUM(t.quantia) as total 
                FROM transacoes t
                JOIN categorias c ON t.categoria_id = c.id
                WHERE t.usuario_id = ? AND t.cartao_id = ? AND t.tipo = 'despesa'
                GROUP BY c.nome";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $cardId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>