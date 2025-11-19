<?php
/*
|--------------------------------------------------------------------------
| Controller (AdicionarTransacao)
|--------------------------------------------------------------------------
| Processa o formulário do modal "Adicionar Renda/Despesa".
| Esta versão salva a transação como "global" (sem conta_id).
*/

// Segurança: Inicia a sessão para pegar o ID do usuário
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Se não for POST, não faz nada e volta
    header('Location: ../View/VisaoGeral.php');
    exit;
}

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    die("Erro: Usuário não autenticado.");
}

// 1. Incluir arquivos
// (Usando ../ para subir um nível, partindo de Controller/ para Config/)
require_once '../Config/conexao.php';
require_once '../Model/DashboardModel.php';

// 2. Validar e Coletar Dados
$data = [
    'usuario_id'   => $_SESSION['user_id'],
    'categoria_id' => filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT),
    'nome'         => trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING)),
    'quantia'      => filter_input(INPUT_POST, 'quantia', FILTER_VALIDATE_FLOAT),
    'tipo'         => filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING), // 'renda' ou 'despesa'
    'data'         => filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING), // Formato Y-m-d H:i
    'metodo'       => filter_input(INPUT_POST, 'metodo', FILTER_SANITIZE_STRING)
];

// 3. Verificar se dados essenciais estão presentes
if (empty($data['nome']) || !$data['categoria_id'] || !$data['quantia'] || !$data['tipo'] || !$data['data'] || empty($data['metodo'])) {
    die("Erro: Dados inválidos ou faltando. " . print_r($data, true));
}

// 4. Se for despesa, armazena como negativo (opcional, mas recomendado)
if ($data['tipo'] === 'despesa' && $data['quantia'] > 0) {
    $data['quantia'] = -$data['quantia'];
}

try {
    // 5. Inicializar o Model e Inserir no Banco
    $model = new DashboardModel($pdo);
    $model->addTransaction($data);

    // 6. Redirecionar de volta para a VisaoGeral (Padrão Post-Redirect-Get)
    header('Location: ../View/VisaoGeral.php?status=success');
    exit;

} catch (Exception $e) {
    // Em produção, logue o erro e mostre uma msg amigável
    die("Erro ao salvar a transação: " . $e->getMessage());
}
?>