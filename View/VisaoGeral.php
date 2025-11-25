<?php
/*
|--------------------------------------------------------------------------
| Entry Point (VisaoGeral.php)
|--------------------------------------------------------------------------
| Integração Login + Dashboard
*/

session_start();

// 1. LOGOUT (Se clicar em sair)
if (isset($_GET['sair']) && $_GET['sair'] == 'true') {
    session_destroy();
    header("Location: Login.php");
    exit;
}

// 2. VERIFICAÇÃO DE SEGURANÇA REAL
// Verifica se a sessão 'id' existe (criada no Login.php)
// Se não existir, manda de volta para o login
if (!isset($_SESSION['id'])) {
    header('Location: Login.php');
    exit;
}

// 3. CARREGAMENTO DE ARQUIVOS
// Ajustando para usar o Connection.php que criamos
if (file_exists(__DIR__ . '/../Model/Connection.php')) {
    require_once __DIR__ . '/../Model/Connection.php';
} else {
    // Fallback de segurança
    die("Erro: Arquivo Model/Connection.php não encontrado.");
}

// Carrega Models e Controllers do Dashboard
require_once '../Model/DashboardModel.php';
require_once '../Controller/DashboardController.php';

use Model\Connection;

// 4. INICIALIZAÇÃO
try {
    // Pega a conexão nova
    $pdo = Connection::getInstance();

    // Pega o ID real do usuário logado
    $userId = $_SESSION['id'];

    // Inicializa o MVC do Dashboard
    $model = new DashboardModel($pdo);
    $controller = new DashboardController($model);

    // Renderiza a tela
    $controller->showDashboard($userId);

} catch (Exception $e) {
    die("Erro ao carregar dashboard: " . $e->getMessage());
}
?>