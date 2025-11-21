<?php
/*
|--------------------------------------------------------------------------
| Model (DashboardModel) 
|--------------------------------------------------------------------------
*/
class DashboardM {
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

   

    // Traz o ID para permitir exclus




   

    // Traz o ID e remove limite para o Extrato
    

    // Função para deletar transação
   
}
?>