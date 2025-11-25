<?php
namespace Model;

use PDO;

class AnaliseModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // 1. Dados para o Gráfico de Linha
    public function getEvolucaoSemestral($usuarioId)
    {
        $sql = "SELECT 
                    DATE_FORMAT(data_transacao, '%Y-%m') as mes_ano,
                    SUM(CASE WHEN tipo = 'renda' THEN quantia ELSE -quantia END) as saldo_mensal
                FROM transacoes
                WHERE usuario_id = :uid 
                  AND data_transacao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY mes_ano
                ORDER BY mes_ano ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Dados para o Gráfico de Pizza
    public function getDespesasPorCategoria($usuarioId, $mes, $ano)
    {
        $sql = "SELECT 
                    c.nome as categoria,
                    SUM(ABS(t.quantia)) as total
                FROM transacoes t
                JOIN categorias c ON t.categoria_id = c.id
                WHERE t.usuario_id = :uid 
                  AND t.tipo = 'despesa'
                  AND MONTH(t.data_transacao) = :mes
                  AND YEAR(t.data_transacao) = :ano
                GROUP BY c.nome
                ORDER BY total DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $usuarioId, ':mes' => $mes, ':ano' => $ano]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Resumo Renda/Despesa
    public function getResumoMes($usuarioId, $mes, $ano)
    {
        $sql = "SELECT tipo, SUM(quantia) as total 
                FROM transacoes 
                WHERE usuario_id = :uid 
                  AND MONTH(data_transacao) = :mes
                  AND YEAR(data_transacao) = :ano
                GROUP BY tipo";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $usuarioId, ':mes' => $mes, ':ano' => $ano]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // 4. (NOVO) Total de Metas (Transações vinculadas a metas)
    public function getTotalMetas($usuarioId, $mes, $ano) {
        $sql = "SELECT SUM(quantia) FROM transacoes 
                WHERE usuario_id = :uid 
                AND meta_id IS NOT NULL 
                AND MONTH(data_transacao) = :mes 
                AND YEAR(data_transacao) = :ano";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $usuarioId, ':mes' => $mes, ':ano' => $ano]);
        return (float) $stmt->fetchColumn();
    }

    // 5. (NOVO) Total de Investimentos (Aportes/Compras)
    public function getTotalInvestimentos($usuarioId, $mes, $ano) {
        $sql = "SELECT SUM(valor_total) FROM investimentos_transacoes 
                WHERE usuario_id = :uid 
                AND tipo_operacao = 'compra' 
                AND MONTH(data_transacao) = :mes 
                AND YEAR(data_transacao) = :ano";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $usuarioId, ':mes' => $mes, ':ano' => $ano]);
        return (float) $stmt->fetchColumn();
    }
}
?>