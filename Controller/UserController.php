<?php
// ... (imports e inclusão do model User) ...
require_once '../Model/User.php';

if (!isset($conn)) {
    die("Erro: Conexão com o banco de dados não estabelecida.");
}

// ----------------------------------------------------
// 1. Instancia o Model (Se já não estiver no topo da página)
// ----------------------------------------------------
$userModel = new User($conn);

// ----------------------------------------------------
// 2. Determina o Cartão Selecionado
// ----------------------------------------------------
$selectedCardId = null;

// Tenta obter o ID do cartão da URL (ex: cartoes.php?card_id=19)
if (isset($_GET['card_id']) && is_numeric($_GET['card_id'])) {
    $selectedCardId = (int)$_GET['card_id'];
} else if (!empty($cartoes)) {
    // Se nenhum ID for passado, usa o ID do primeiro cartão como padrão
    $selectedCardId = (int)$cartoes[0]['id'];
}

// ----------------------------------------------------
// 3. Busca e Calcula os Dados (USANDO O FILTRO)
// ----------------------------------------------------

// Renda e Despesas Totais
$totalRenda = $userModel->calculateTotalIncome($selectedCardId);
$totalDespesas = $userModel->calculateTotalExpenses($selectedCardId);

// Últimas Transações
$latestTransactions = $userModel->getLatestTransactions($selectedCardId, 10);

// Despesas por Categoria (para o Gráfico e Lista)
$expensesByCategory = $userModel->getExpensesByCategory($selectedCardId);

// ... (Prepara Dados para o Gráfico e Mapeamento de Cores) ...
// ... (o restante do controller se mantém igual) ...
