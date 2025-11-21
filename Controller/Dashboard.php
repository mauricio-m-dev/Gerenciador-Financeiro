<?php
/*
|--------------------------------------------------------------------------
| Controller (DashboardController) - Versão Global
|--------------------------------------------------------------------------
| Orquestra a lógica da página (versão global).
*/
class Dashboard {
    private $model;

    public function __construct(DashboardM $model) {
        $this->model = $model;
    }

    // Não precisamos mais de $selectedAccountId
    public function showDashboard($userId) {
        
        // --- 1. Buscar Dados (Globais) ---
        $user = $this->model->getUserDetails($userId);
        $userName = $user['nome'] ?? 'Usuário';
        // Corrigido para o nome da coluna do seu Model (Cód 6)
        $userProfilePic = $user['profile_pic_url'] ?? '../template/asset/img/default-profile.png'; 

        // Dados dos Cards (Globais)
        $totals = $this->model->getGlobalTotals($userId);
        $totalRenda = $totals['totalRenda'];
        $totalDespesas = $totals['totalDespesas']; // Model já retorna o valor correto
        
        // Card de Metas
        $totalMetas = $this->model->getTotalMetas($userId);

        // Tabela de Transações Recentes (Globais)
        $transactions = $this->model->getRecentTransactions($userId, 999999999999999999);
        
        // Gráfico de Despesas (Globais)
        $categoryData = $this->model->getCategoryExpenses($userId);

        // --- 2. Preparar Dados para a View ---
        
        // CORREÇÃO: Removido o json_encode e htmlspecialchars daqui.
        // O array $categoryData será passado diretamente para a View,
        // que irá codificá-lo no <script id="app-data">.
        // $chartLabelsJSON = ... (REMOVIDO)
        // $chartValoresJSON = ... (REMOVIDO)


        // Buscar categorias para preencher o modal
        $categoriasRenda = $this->model->getCategorias($userId, 'renda');
        $categoriasDespesa = $this->model->getCategorias($userId, 'despesa');

        // --- 3. Carregar a View ---
        // A View (VisaoGeralView.php) terá acesso a todas as
        // variáveis declaradas aqui ($userName, $totalRenda, $categoryData, etc.)
        require_once '../View/Analise.php';
    }
}
?>