<?php
// Incluir conexão com o banco
require_once 'conexao.php';

// Receber dados do formulário
$nomeCartao = trim($_POST['nomeCartao']);
$numeroCartao = preg_replace('/\D/', '', $_POST['numeroCartao']); // remove espaços e não-dígitos
$validadeCartao = $_POST['validadeCartao'];
$cvvCartao = $_POST['cvvCartao'];
$bandeiraCartao = $_POST['bandeiraCartao'];
$tipoCartao = $_POST['tipoCartao'];
$limiteCartao = !empty($_POST['limiteCartao']) ? floatval($_POST['limiteCartao']) : null;

// Validação básica
$erros = [];

if(strlen($numeroCartao) < 13 || strlen($numeroCartao) > 19){
    $erros[] = "Número do cartão inválido.";
}

if(strlen($cvvCartao) < 3 || strlen($cvvCartao) > 4){
    $erros[] = "CVV inválido.";
}

if(empty($nomeCartao) || empty($validadeCartao) || empty($bandeiraCartao) || empty($tipoCartao)){
    $erros[] = "Todos os campos obrigatórios devem ser preenchidos.";
}

if($tipoCartao == 'credito' && empty($limiteCartao)){
    $erros[] = "Limite obrigatório para cartão de crédito.";
}

// Se houver erros, exibe e interrompe
if(!empty($erros)){
    foreach($erros as $erro){
        echo "<p>$erro</p>";
    }
    echo "<a href='javascript:history.back()'>Voltar</a>";
    exit;
}

// Armazenar apenas os últimos 4 dígitos do cartão
$ultimos4 = substr($numeroCartao, -4);

// Inserir no banco
$stmt = $conn->prepare("INSERT INTO cartoes (nome, ultimos4, validade, cvv, bandeira, tipo, limite) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssd", $nomeCartao, $ultimos4, $validadeCartao, $cvvCartao, $bandeiraCartao, $tipoCartao, $limiteCartao);

if($stmt->execute()){
    echo "<p>Cartão cadastrado com sucesso!</p>";
    echo "<a href='../view/cartao.php'>Voltar ao painel</a>";
}else{
    echo "Erro ao cadastrar cartão: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
