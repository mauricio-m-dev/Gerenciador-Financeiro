<?php
session_start();

$raiz = dirname(__DIR__);
if (file_exists($raiz . '/Config/configuration.php')) {
    require_once $raiz . '/Config/configuration.php';
} else {
    require_once $raiz . '/Config/conexao.php';
}

$userId = $_SESSION['id'] ?? 0;
if ($userId <= 0) { http_response_code(403); die("Erro: Não logado"); }

try {
    $conn = getConexao();

    $nome = trim($_POST['nomeCartao']);
    $numero = preg_replace('/\D/', '', $_POST['numeroCartao']);
    $validade = $_POST['validadeCartao'] . '-01';
    $bandeira = $_POST['bandeiraCartao'];
    $tipo = $_POST['tipoCartao'];
    $limite = !empty($_POST['limiteCartao']) ? (float)$_POST['limiteCartao'] : 0;
    $last4 = substr($numero, -4);

    $sql = "INSERT INTO cartoes (usuario_id, nome, ultimos4, validade, bandeira, tipo, limite) 
            VALUES (:uid, :nome, :last4, :val, :band, :tipo, :lim)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':uid' => $userId, ':nome' => $nome, ':last4' => $last4, ':val' => $validade,
        ':band' => $bandeira, ':tipo' => $tipo, ':lim' => $limite
    ]);

    http_response_code(200);

} catch (Exception $e) {
    http_response_code(500);
    echo "Erro: " . $e->getMessage();
}
?>