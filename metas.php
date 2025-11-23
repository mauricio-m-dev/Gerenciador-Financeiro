<?php
// Carrega o autoloader do composer
require_once 'Config/configuration.php'; // Ajuste o caminho
require_once 'vendor/autoload.php'; // Ajuste o caminho

use Helper\FunctionsHelper;

// Define que erros de PHP NÃO devem ser exibidos (para não quebrar JSON)
// Em produção, configure error_log no php.ini para registrar erros
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Funções globais auxiliares (Wrappers do FunctionsHelper)
// ... (mantenha as funções 'formatar_moeda', 'calcular_progresso', etc.) ...

// Inclui o Model e o Controller
require_once 'Model/MetaModel.php'; // Ajuste o caminho
require_once 'Controller/MetaController.php'; // Ajuste o caminho

// Instancia o Controller
$controller = new MetaController();

$action = $_GET['action'] ?? 'index';


if ($action === 'create') {
    $controller->create();
    exit; 
} elseif ($action === 'contribute') {
    $controller->contribute();
    exit; 
} elseif ($action === 'delete') {
    $controller->delete();
    exit; 
} elseif ($action === 'undo') { // ADICIONE ESTE BLOCO
    $controller->undo();
    exit;
} else {
    $controller->index();
}
?>