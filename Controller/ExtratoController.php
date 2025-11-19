<?php
/*
|--------------------------------------------------------------------------
| Controller (ExtratoController)
|--------------------------------------------------------------------------
| Orquestra a lógica da página de Extrato Completo.
*/
class ExtratoController {
    
    private $model;

    public function __construct(DashboardModel $model) {
        $this->model = $model;
    }

    /**
     * Função auxiliar para agrupar transações por data.
     * Ela pega a data do SQL (ex: 2025-10-21 15:00:00)
     * e a formata (ex: 21 Out 2025) para usar como chave.
     */
    private function groupTransactionsByDate(array $transactions): array
    {
        $grouped = [];
        // Lista de meses em português (para formatar a data)
        $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        
        foreach ($transactions as $t) {
            // Formata a data do banco
            $timestamp = strtotime($t['data']);
            $date = date('d', $timestamp) . ' ' . $meses[date('n', $timestamp) - 1] . ' ' . date('Y', $timestamp);
            
            if (!isset($grouped[$date])) {
                $grouped[$date] = [];
            }
            $grouped[$date][] = $t;
        }
        return $grouped;
    }

    // Método principal
    public function showExtrato($userId) {
        
        // --- 1. Buscar Dados ---
        
        // Pega os dados do usuário (para o header)
        $user = $this->model->getUserDetails($userId);
        $userName = $user['nome'] ?? 'Usuário';
        $userProfilePic = $user['profile_pic_url'] ?? '../template/asset/img/default-profile.png'; 

        // Pega TODAS as transações (flat array)
        $allTransactions = $this->model->getAllTransactions($userId);

        // Agrupa as transações por data (como a simulação fazia)
        $groupedTransactions = $this->groupTransactionsByDate($allTransactions);

        // --- 2. Carregar a View ---
        // A View/ExtratoView.php terá acesso a $userName, $userProfilePic e $groupedTransactions
        require_once '../View/ExtratoView.php';
    }
}
?>