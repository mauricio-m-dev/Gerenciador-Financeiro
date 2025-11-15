<?php
// Não é estritamente necessário se você não usa sessões, mas é bom manter o hábito
session_start(); 
header('Content-Type: application/json'); // Garante que a resposta seja JSON

// Ajuste o caminho para a sua conexão com o banco de dados
require_once '../Config/conexao.php'; 

$response = ['success' => false, 'message' => 'Requisição inválida.'];

// Verifica se a requisição é um POST e se o ID do cartão foi fornecido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cartao'])) {
    
    // Filtra e sanitiza a entrada para garantir que é um número inteiro
    $id_cartao = filter_var($_POST['id_cartao'], FILTER_VALIDATE_INT);

    if ($id_cartao === false || $id_cartao <= 0) {
        $response['message'] = 'ID de cartão inválido ou ausente.';
        http_response_code(400); // Bad Request
    } else {
        try {
            // SQL CORRIGIDO: Deleta apenas baseado no ID do cartão (coluna 'id')
            $sql = "DELETE FROM cartoes WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Erro ao preparar a declaração SQL: " . $conn->error);
            }

            // Bind dos parâmetros: apenas 1 parâmetro inteiro ('i')
            $stmt->bind_param("i", $id_cartao); 

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    // Sucesso na exclusão
                    $response['success'] = true;
                    $response['message'] = 'Cartão deletado com sucesso.';
                    http_response_code(200);
                } else {
                    // Nenhum cartão encontrado
                    $response['message'] = 'Cartão não encontrado no banco de dados.';
                    http_response_code(404);
                }
            } else {
                // Erro na execução
                throw new Exception("Erro ao executar a exclusão: " . $stmt->error);
            }

            $stmt->close();
        } catch (Exception $e) {
            $response['message'] = 'Erro interno do servidor: ' . $e->getMessage();
            http_response_code(500); // Internal Server Error
        }
    }
} else {
    $response['message'] = 'Método de requisição inválido ou dados ausentes.';
    http_response_code(405); // Method Not Allowed
}

echo json_encode($response);
exit;
?>