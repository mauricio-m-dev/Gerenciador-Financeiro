<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/VisaoGeral.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../View/Login.php');
    exit;
}

require_once '../Config/conexao.php';
require_once '../Model/DashboardModel.php';

$idTransacao = filter_input(INPUT_POST, 'id_transacao', FILTER_VALIDATE_INT);
$origin      = filter_input(INPUT_POST, 'origin', FILTER_SANITIZE_SPECIAL_CHARS); // 'extrato' ou 'dashboard'

$redirectUrl = ($origin === 'extrato') ? '../View/Extrato.php' : '../View/VisaoGeral.php';

if ($idTransacao) {
    try {
        $model = new DashboardModel($pdo);
        $model->deleteTransaction($idTransacao, $_SESSION['user_id']);
        header("Location: $redirectUrl?msg=deleted");
        exit;
    } catch (Exception $e) {
        header("Location: $redirectUrl?msg=error");
        exit;
    }
} else {
    header("Location: $redirectUrl?msg=invalid");
    exit;
}
?>