<?php
/**
 * init.php
 * Arquivo de inicialização que carrega as classes necessárias
 */

// Defina o caminho da raiz do projeto
define('ROOT_PATH', dirname(__DIR__));

// Autoload de classes
spl_autoload_register(function ($class) {
    // Models
    if (file_exists(ROOT_PATH . '/app/Models/' . $class . '.php')) {
        require ROOT_PATH . '/app/Models/' . $class . '.php';
    }
    // Controllers
    elseif (file_exists(ROOT_PATH . '/app/Controllers/' . $class . '.php')) {
        require ROOT_PATH . '/app/Controllers/' . $class . '.php';
    }
    // Config
    elseif (file_exists(ROOT_PATH . '/config/' . $class . '.php')) {
        require ROOT_PATH . '/config/' . $class . '.php';
    }
});

try {
    // Conecta ao banco de dados
    $database = new Database();
    $pdo = $database->getPDO();

    // Inicializa os models
    $ativoModel = new Ativo($pdo);
    $transacaoModel = new InvestimentoTransacao($pdo);

    // Inicializa o controller
    $investimentoController = new InvestimentoController($ativoModel, $transacaoModel);
} catch (Exception $e) {
    // Se der erro na inicialização, lançar para ser capturado em api/investimento.php
    throw new Exception('Erro ao inicializar: ' . $e->getMessage());
}
?>
