<?php
session_start();
header('Content-Type: application/json');

$raiz = dirname(__DIR__);
if (file_exists($raiz . '/Config/configuration.php')) {
    require_once $raiz . '/Config/configuration.php';
} else {
    require_once $raiz . '/Config/conexao.php';
}

$userId = $_SESSION['id'] ?? 0;
$idCard = $_POST['id_cartao'] ?? 0;

if ($userId > 0 && $idCard > 0) {
    try {
        $conn = getConexao();
        // Garante que só deleta se o cartão pertencer ao usuário logado
        $stmt = $conn->prepare("DELETE FROM cartoes WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$idCard, $userId]);
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
}
?>