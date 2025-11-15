<?php
header('Content-Type: application/json');
require_once '../Model/User.php';
require_once '../Config/conexao.php'; // ajuste se necessário

// 1. Recebe o ID do cartão
$cardId = 0;
if (isset($_GET['cartao_id']) && is_numeric($_GET['cartao_id'])) {
    $cardId = (int)$_GET['cartao_id'];
}

// 2. Instancia o Model User
$userModel = new User($conn);

// 3. Busca somente despesas por categoria
$expensesByCategory = $userModel->getExpensesByCategory($cardId);

// 4. Prepara dados para o gráfico
$grafico_labels = array_column($expensesByCategory, 'label');
$grafico_valores = array_column($expensesByCategory, 'porcentagem');

// 5. Retorna APENAS o que o gráfico precisa
$response = [
    "grafico" => [
        "labels" => $grafico_labels,
        "data"   => $grafico_valores
    ]
];

echo json_encode($response);
exit;
