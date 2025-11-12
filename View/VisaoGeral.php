<?php
/*
|--------------------------------------------------------------------------
| Entry Point (VisaoGeral.php)
|--------------------------------------------------------------------------
| Este arquivo é o "Roteador". Ele inicializa o MVC.
| O usuário acessa este arquivo.
*/

// Inicia a sessão
session_start();

// SIMULAÇÃO DE LOGIN: (Para testes)
// Em produção, seu Login.php fará isso.
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Força o login como usuário ID 1
}

// Verifica se o usuário está logado (redundante, mas seguro)
if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php'); // Redireciona para o login
    exit;
}

// 1. Incluir arquivos necessários (Corrigido com ../)
// Este arquivo (VisaoGeral.php) está em View/,
// então ele sobe um nível (../) para achar Config/, Model/, etc.
require_once '../Config/conexao.php';
require_once '../Model/DashboardModel.php';
require_once '../Controller/DashboardController.php';

// 2. Obter dados da requisição
$userId = $_SESSION['user_id'];

// 3. Inicializar o MVC
$model = new DashboardModel($pdo);
$controller = new DashboardController($model);

// 4. Chamar o Controller
// O Controller fará todo o trabalho e, no final,
// chamará 'require_once ../View/VisaoGeralView.php'
$controller->showDashboard($userId);

?>