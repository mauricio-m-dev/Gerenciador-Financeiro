<?php
/*
|--------------------------------------------------------------------------
| Entry Point (Extrato.php)
|--------------------------------------------------------------------------
| Inicializa o MVC para a página de Extrato.
| O usuário acessa este arquivo.
*/

// Inicia a sessão
session_start();

// Simulação de Login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; 
}
if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit;
}

// 1. Incluir arquivos necessários (sobe um nível: ../)
require_once '../Config/conexao.php';
require_once '../Model/DashboardModel.php';
require_once '../Controller/ExtratoController.php'; // <-- Chama o Controller de Extrato

// 2. Obter dados da requisição
$userId = $_SESSION['user_id'];

// 3. Inicializar o MVC
$model = new DashboardModel($pdo);
$controller = new ExtratoController($model); // <-- Cria o Controller de Extrato

// 4. Chamar o Controller
$controller->showExtrato($userId); // <-- Chama o método de Extrato

?>