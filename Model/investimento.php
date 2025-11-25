<?php
namespace Model;

use PDO;

class Investimento
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Adicionar Compra ou Venda
    public function adicionarTransacao($usuarioId, $ativoId, $tipoOperacao, $qtd, $valorUnit, $corretora)
    {
        $sql = "INSERT INTO investimentos_transacoes 
                (usuario_id, ativo_id, tipo_operacao, quantidade, valor_unitario, corretora, data_transacao)
                VALUES (:uid, :aid, :tipo, :qtd, :val, :corr, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':uid' => $usuarioId,
            ':aid' => $ativoId,
            ':tipo' => $tipoOperacao,
            ':qtd' => $qtd,
            ':val' => $valorUnit,
            ':corr' => $corretora
        ]);
    }

    // Remover transação
    public function removerTransacao($id, $usuarioId)
    {
        $sql = "DELETE FROM investimentos_transacoes WHERE id = :id AND usuario_id = :uid";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id, ':uid' => $usuarioId]);
    }

    // CARTEIRA CONSOLIDADA (Corrigido o erro SQLSTATE[42S22])
    public function obterCarteira($usuarioId)
    {
        $sql = "SELECT 
                    a.id as ativo_id,
                    a.simbolo,
                    a.nome,
                    a.tipo,
                    SUM(CASE WHEN t.tipo_operacao = 'compra' THEN t.quantidade 
                             WHEN t.tipo_operacao = 'venda' THEN -t.quantidade 
                             ELSE 0 END) as total_qtd,
                    SUM(CASE WHEN t.tipo_operacao = 'compra' THEN t.valor_total 
                             WHEN t.tipo_operacao = 'venda' THEN -t.valor_total 
                             ELSE 0 END) as valor_investido_liquido
                FROM investimentos_transacoes t
                JOIN ativos a ON t.ativo_id = a.id
                WHERE t.usuario_id = :uid
                GROUP BY a.id, a.simbolo, a.nome, a.tipo
                HAVING total_qtd > 0
                ORDER BY valor_investido_liquido DESC"; 
                // AQUI ESTAVA O ERRO: Simplifiquei para ordenar pelo valor total direto.

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Histórico de operações
    public function obterHistorico($usuarioId)
    {
        $sql = "SELECT t.*, a.simbolo, a.nome 
                FROM investimentos_transacoes t
                JOIN ativos a ON t.ativo_id = a.id
                WHERE t.usuario_id = :uid
                ORDER BY t.data_transacao DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>