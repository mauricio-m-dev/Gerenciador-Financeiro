<?php
/*
|--------------------------------------------------------------------------
| Controller (ExtratoController) - Sem Foto
|--------------------------------------------------------------------------
*/
class ExtratoController {
    
    private $model;

    public function __construct(DashboardModel $model) {
        $this->model = $model;
    }

    private function groupTransactionsByDate(array $transactions): array {
        $grouped = [];
        $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        
        foreach ($transactions as $t) {
            $timestamp = strtotime($t['data']);
            $date = date('d', $timestamp) . ' ' . $meses[date('n', $timestamp) - 1] . ' ' . date('Y', $timestamp);
            
            if (!isset($grouped[$date])) {
                $grouped[$date] = [];
            }
            $grouped[$date][] = $t;
        }
        return $grouped;
    }

    public function showExtrato($userId) {
        
        $user = $this->model->getUserDetails($userId);
        $userName = $user['nome'] ?? 'Usuário';
        
        // FIX: Imagem padrão fixa
        $userProfilePic = '../template/asset/img/default-profile.png'; 

        $allTransactions = $this->model->getAllTransactions($userId);
        $groupedTransactions = $this->groupTransactionsByDate($allTransactions);

        require_once '../View/ExtratoView.php';
    }
}
?>