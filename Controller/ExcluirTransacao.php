<?php
/*
|--------------------------------------------------------------------------
| Action: Excluir Transação
|--------------------------------------------------------------------------
*/
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/VisaoGeral.php');
    exit;
}

// Verifica Login (ATENÇÃO: Mudado para 'id')
if (!isset($_SESSION['id'])) {
    header('Location: ../View/Login.php');
    exit;
}

// Correção dos Caminhos
if (file_exists(__DIR__ . '/../Model/Connection.php')) {
    require_once __DIR__ . '/../Model/Connection.php';
    require_once __DIR__ . '/../Model/DashboardModel.php';
} else {
    die("Erro: Arquivos de Model não encontrados.");
}

use Model\Connection;

$idTransacao = filter_input(INPUT_POST, 'id_transacao', FILTER_VALIDATE_INT);
$origin      = filter_input(INPUT_POST, 'origin', FILTER_SANITIZE_SPECIAL_CHARS); // 'extrato' ou 'dashboard'

// Define para onde voltar
$redirectUrl = ($origin === 'extrato') ? '../View/Extrato.php' : '../View/VisaoGeral.php';

if ($idTransacao) {
    try {
        $pdo = Connection::getInstance();
        $model = new DashboardModel($pdo);
        
        // Passa o ID do usuário para garantir que ele só apague as próprias transações
        $model->deleteTransaction($idTransacao, $_SESSION['id']);
        
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