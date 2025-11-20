<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/VisaoGeral.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    die("Erro: Usuário não autenticado.");
}

require_once '../Config/conexao.php';
require_once '../Model/DashboardModel.php';

$data = [
    'usuario_id'   => $_SESSION['user_id'],
    'categoria_id' => filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT),
    'nome'         => trim($_POST['nome'] ?? ''),
    'quantia'      => filter_input(INPUT_POST, 'quantia', FILTER_VALIDATE_FLOAT),
    'tipo'         => trim($_POST['tipo'] ?? ''),
    'data'         => trim($_POST['data'] ?? ''),
    'metodo'       => trim($_POST['metodo'] ?? '')
];

if (empty($data['nome']) || !$data['categoria_id'] || !is_numeric($data['quantia']) || empty($data['tipo']) || empty($data['data']) || empty($data['metodo'])) {
    header('Location: ../View/VisaoGeral.php?error=missing_fields');
    exit;
}

// Ajuste de Despesa (Negativo)
if ($data['tipo'] === 'despesa' && $data['quantia'] > 0) {
    $data['quantia'] = -$data['quantia'];
}

try {
    $model = new DashboardModel($pdo);
    $model->addTransaction($data);
    header('Location: ../View/VisaoGeral.php?status=success');
    exit;

} catch (Exception $e) {
    header('Location: ../View/VisaoGeral.php?status=error');
    exit;
}
?>