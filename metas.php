<?php
// Arquivo: metas.php (Roteador)

// 1. Configurações de Erro (Esconde erros visuais para não quebrar o JSON)
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

// 2. Inicia Sessão e Dependências
session_start();

// Ajuste os caminhos conforme sua estrutura de pastas
if (file_exists('Config/configuration.php')) {
    require_once 'Config/configuration.php';
} elseif (file_exists('db.php')) {
    require_once 'db.php';
}

require_once 'Model/MetaModel.php';
require_once 'Controller/MetaController.php';

// 3. Define o ID do Usuário
// Tenta pegar da sessão. Se não tiver (teste), usa o ID 3 (Mauricio)
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} elseif (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    $userId = 3; // Fallback para testes se não estiver logado
}

// Verifica se a conexão existe
if (!function_exists('getConexao')) {
    echo json_encode(['status' => 'error', 'message' => 'Função de conexão não encontrada.']);
    exit;
}

try {
    $pdo = getConexao();
    $controller = new MetaController($pdo);
    
    $action = $_GET['action'] ?? '';

    // 4. Roteamento (Passando o $userId obrigatório)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        switch ($action) {
            case 'create':
                // AQUI ERA O ERRO: Precisamos passar $userId
                $controller->create($userId);
                break;
            case 'contribute':
                $controller->contribute($userId);
                break;
            case 'undo':
                $controller->undo($userId);
                break;
            case 'delete':
                $controller->delete($userId);
                break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Ação inválida: ' . $action]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Método inválido (use POST)']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro Interno: ' . $e->getMessage()]);
}
?>