<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit;
}

require_once '../Config/conexao.php';
require_once '../Model/DashboardModel.php';
require_once '../Controller/ExtratoController.php';

$userId = $_SESSION['user_id'];

$model = new DashboardModel($pdo);
$controller = new ExtratoController($model);

$controller->showExtrato($userId);
?>