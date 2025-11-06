<?php
// Incluir conexão com o banco
require_once 'conexao.php';

// ATENÇÃO: Se 'conexao.php' não fizer tratamento de erro na conexão,
// adicione o 'require' do arquivo 'conexao.php'
// E certifique-se que $conn está disponível aqui.

// Receber dados do formulário
$nomeCartao = trim($_POST['nomeCartao']);
$numeroCartao = preg_replace('/\D/', '', $_POST['numeroCartao']); // remove espaços e não-dígitos
$validadeCartao = $_POST['validadeCartao'];
$bandeiraCartao = $_POST['bandeiraCartao'];
$tipoCartao = $_POST['tipoCartao'];
// Tratamento do Limite (garante float ou null se vazio/não crédito)
$limiteCartao = ($tipoCartao == 'credito' && !empty($_POST['limiteCartao'])) ? floatval($_POST['limiteCartao']) : null;


// Processamento de Dados Necessário para MySQL
// 1. Armazenar apenas os últimos 4 dígitos do cartão
$ultimos4 = substr($numeroCartao, -4);
// 2. Corrigir formato da validade (adicionar dia para o tipo DATE)
$validadeFormatada = $validadeCartao . '-01';


// Validação básica
$erros = [];

if(strlen($numeroCartao) < 13 || strlen($numeroCartao) > 19){
    $erros[] = "Número do cartão inválido.";
}
if(empty($nomeCartao) || empty($validadeCartao) || empty($bandeiraCartao) || empty($tipoCartao)){
    $erros[] = "Todos os campos obrigatórios devem ser preenchidos.";
}
if($tipoCartao == 'credito' && is_null($limiteCartao)){
    $erros[] = "Limite obrigatório para cartão de crédito.";
}

// ----------------------------------------------------------------------
// 🚨 AJUSTE CRUCIAL 1: Tratamento de Erros de Validação para AJAX
// ----------------------------------------------------------------------
if(!empty($erros)){
    // Retorna status HTTP 400 (Bad Request) para o Fetch API
    http_response_code(400); 
    // Retorna a primeira mensagem de erro para o JavaScript exibir
    echo array_pop($erros); 
    exit;
}

// Inserir no banco
$stmt = $conn->prepare("INSERT INTO cartoes (nome, ultimos4, validade, bandeira, tipo, limite) VALUES (?, ?, ?, ?, ?, ?)");

// Verifica se a preparação falhou (ex: erro de sintaxe)
if ($stmt === false) {
    http_response_code(500); 
    echo "Erro de Preparação do SQL: " . $conn->error;
    exit;
}

// O bind_param usa a variável $ultimos4 e $validadeFormatada
$stmt->bind_param("sssssd", $nomeCartao, $ultimos4, $validadeFormatada, $bandeiraCartao, $tipoCartao, $limiteCartao);


if($stmt->execute()){
    // ----------------------------------------------------------------------
    // 🚨 AJUSTE CRUCIAL 2: Sucesso para AJAX
    // ----------------------------------------------------------------------
    // Sucesso! Retorna o status HTTP 200 (OK) sem imprimir NADA no corpo
    $stmt->close();
    $conn->close();
    exit; // Encerra o script com sucesso (HTTP 200)
}else{
    // ----------------------------------------------------------------------
    // 🚨 AJUSTE CRUCIAL 3: Falha na Execução para AJAX
    // ----------------------------------------------------------------------
    // Erro ao executar a query (ex: duplicação)
    http_response_code(500); 
    echo "Erro ao cadastrar cartão: " . $stmt->error;
    $stmt->close();
    $conn->close();
    exit;
}
?>