<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

$raiz = dirname(__DIR__);

// Fallback de conexão
if (file_exists($raiz . '/Model/Connection.php')) {
    require_once $raiz . '/Model/Connection.php';
} else if (file_exists($raiz . '/Config/configuration.php')) {
    require_once $raiz . '/Config/configuration.php';
}

require_once $raiz . '/Model/CartaoModel.php';
require_once $raiz . '/Controller/CartaoController.php';

use Model\Connection;
use Model\CartaoModel;
use Controller\CartaoController;

try {
    $pdo = Connection::getInstance();
    $model = new CartaoModel($pdo);
    $controller = new CartaoController($model);

    $cardId = isset($_GET['card_id']) ? (int)$_GET['card_id'] : 0;
    
    // Pega dados do controller
    $data = $controller->getDadosJson($_SESSION['id'], $cardId);
    
    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>