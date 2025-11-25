<?php
/*
|--------------------------------------------------------------------------
| Action: Adicionar Transação
|--------------------------------------------------------------------------
*/
session_start();

// Verifica se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/VisaoGeral.php');
    exit;
}

// Verifica Login (ATENÇÃO: Mudado para 'id')
if (!isset($_SESSION['id'])) {
    header('Location: ../View/Login.php');
    exit;
}

// Correção dos Caminhos e Conexão
if (file_exists(__DIR__ . '/../Model/Connection.php')) {
    require_once __DIR__ . '/../Model/Connection.php';
    require_once __DIR__ . '/../Model/DashboardModel.php';
} else {
    die("Erro: Arquivos de Model não encontrados.");
}

use Model\Connection;

// Prepara dados
$data = [
    'usuario_id'   => $_SESSION['id'], // Mudado de user_id para id
    'categoria_id' => filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT),
    'nome'         => trim($_POST['nome'] ?? ''),
    'quantia'      => filter_input(INPUT_POST, 'quantia', FILTER_VALIDATE_FLOAT),
    'tipo'         => trim($_POST['tipo'] ?? ''), // renda ou despesa
    'data'         => trim($_POST['data'] ?? ''),
    'metodo'       => trim($_POST['metodo'] ?? '')
];

// Validação simples
if (empty($data['nome']) || !$data['categoria_id'] || !is_numeric($data['quantia']) || empty($data['tipo']) || empty($data['data'])) {
    header('Location: ../View/VisaoGeral.php?error=missing_fields');
    exit;
}

// Ajuste de Despesa (Torna negativo se for despesa)
if ($data['tipo'] === 'despesa' && $data['quantia'] > 0) {
    $data['quantia'] = -$data['quantia'];
}
// Ajuste de Renda (Torna positivo se for renda)
if ($data['tipo'] === 'renda' && $data['quantia'] < 0) {
    $data['quantia'] = abs($data['quantia']);
}

try {
    // Instancia conexão e Model
    $pdo = Connection::getInstance();
    $model = new DashboardModel($pdo);
    
    // Salva
    if ($model->addTransaction($data)) {
        header('Location: ../View/VisaoGeral.php?msg=success');
    } else {
        header('Location: ../View/VisaoGeral.php?error=db_error');
    }
} catch (Exception $e) {
    header('Location: ../View/VisaoGeral.php?error=exception');
}
exit;
?>