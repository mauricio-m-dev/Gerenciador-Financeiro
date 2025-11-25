<?php
/*
|--------------------------------------------------------------------------
| Controller (DashboardController) - Sem Foto
|--------------------------------------------------------------------------
*/
class DashboardController {
    private $model;

    public function __construct(DashboardModel $model) {
        $this->model = $model;
    }

    public function showDashboard($userId) {
        
        // 1. Buscar Dados
        $user = $this->model->getUserDetails($userId);
        $userName = $user['nome'] ?? 'Usuário';
        
        // FIX: Usa sempre a imagem padrão, sem buscar no banco
        $userProfilePic = '../template/asset/img/default-profile.png'; 

        // 2. Dados dos Cards
        $totals = $this->model->getGlobalTotals($userId);
        $totalRenda = $totals['totalRenda'];
        $totalDespesas = $totals['totalDespesas'];
        
        $totalMetas = $this->model->getTotalMetas($userId);
        $transactions = $this->model->getRecentTransactions($userId, 5);
        $categoryData = $this->model->getCategoryExpenses($userId);

        $categoriasRenda = $this->model->getCategorias($userId, 'renda');
        $categoriasDespesa = $this->model->getCategorias($userId, 'despesa');

        // 3. Carregar View
        require_once '../View/VisaoGeralView.php';
    }
}
?>