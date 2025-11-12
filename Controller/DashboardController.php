<?php
/*
|--------------------------------------------------------------------------
| Controller (DashboardController) - Versão Global
|--------------------------------------------------------------------------
| Orquestra a lógica da página (versão global).
*/
class DashboardController {
    private $model;

    public function __construct(DashboardModel $model) {
        $this->model = $model;
    }

    // Não precisamos mais de $selectedAccountId
    public function showDashboard($userId) {
        
        // --- 1. Buscar Dados (Globais) ---
        $user = $this->model->getUserDetails($userId);
        $userName = $user['nome'] ?? 'Usuário';
        $userProfilePic = $user['profile_pic_url'] ?? 'https://via.placeholder.com/40';

        // Dados dos Cards (Globais)
        $totals = $this->model->getGlobalTotals($userId);
        $totalRenda = $totals['totalRenda'];
        $totalDespesas = $totals['totalDespesas'];
        
        // Card de Metas
        $totalMetas = $this->model->getTotalMetas($userId);

        // Tabela de Transações Recentes (Globais)
        $transactions = $this->model->getRecentTransactions($userId, 5);
        
        // Gráfico de Despesas (Globais)
        $categoryData = $this->model->getCategoryExpenses($userId);

        // --- 2. Preparar Dados para a View ---
        $chartLabelsJSON = htmlspecialchars(json_encode($categoryData['labels']), ENT_QUOTES, 'UTF-8');
        $chartValoresJSON = htmlspecialchars(json_encode($categoryData['valores']), ENT_QUOTES, 'UTF-8');

        // Buscar categorias para preencher o modal
        $categoriasRenda = $this->model->getCategorias($userId, 'renda');
        $categoriasDespesa = $this->model->getCategorias($userId, 'despesa');

        // --- 3. Carregar a View ---
        // A View (VisaoGeralView.php) terá acesso a todas as
        // variáveis declaradas aqui ($userName, $totalRenda, etc.)
        require_once '../View/VisaoGeralView.php';
    }
}
?>