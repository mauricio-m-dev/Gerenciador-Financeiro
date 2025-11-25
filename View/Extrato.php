<?php
session_start();

// Verifica Login com 'id'
if (!isset($_SESSION['id'])) {
    header('Location: Login.php');
    exit;
}

// Conexão correta
if (file_exists(__DIR__ . '/../Model/Connection.php')) {
    require_once __DIR__ . '/../Model/Connection.php';
} else {
    die("Erro: Arquivo Connection.php não encontrado.");
}

require_once '../Model/DashboardModel.php';
require_once '../Controller/ExtratoController.php';

use Model\Connection;

$userId = $_SESSION['id'];

try {
    $pdo = Connection::getInstance();
    $model = new DashboardModel($pdo);
    $controller = new ExtratoController($model);
    $controller->showExtrato($userId);
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}
?>