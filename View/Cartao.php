<?php
/*
|--------------------------------------------------------------------------
| Entry Point: Cartao.php
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id'])) {
    header('Location: Login.php');
    exit;
}

// DEFINIÇÃO DE CAMINHOS CORRIGIDA
$baseDir = __DIR__ . '/..'; // Sobe um nível para a raiz do projeto

require_once $baseDir . '/Model/Connection.php';
require_once $baseDir . '/Model/CartaoModel.php';
require_once $baseDir . '/Controller/CartaoController.php';

use Model\Connection;
use Model\CartaoModel;
use Controller\CartaoController;

try {
    // 1. Conexão
    $pdo = Connection::getInstance();
    
    // 2. Instâncias
    $model = new CartaoModel($pdo);
    $controller = new CartaoController($model);

    // 3. Execução da Lógica (Pega ID da URL ou Padrão)
    $data = $controller->index($_SESSION['id']);

    // 4. Extração de Variáveis para usar no HTML abaixo
    $cartoes = $data['cartoes'];
    $activeCardId = $data['activeCardId'];
    $resumo = $data['resumo'];
    $latestTransactions = $data['transacoes']; // Agora com nomes de categorias corretos
    $expensesByCategory = $data['expensesByCategory'];

    // 5. Renderização
    // Certifique-se que o arquivo CartaoView.php está na mesma pasta que este arquivo
    if (file_exists('CartaoView.php')) {
        require_once 'CartaoView.php';
    } else {
        echo "Erro: CartaoView.php não encontrado.";
    }

} catch (Exception $e) {
    die("<b>Erro no Sistema:</b> " . $e->getMessage());
}
?>