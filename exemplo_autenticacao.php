<?php
/**
 * exemplo_autenticacao.php
 * Exemplo de como integrar a API com um sistema de autenticação
 * 
 * IMPORTANTE: Este é um exemplo educacional. Adapte conforme seu sistema de auth.
 */

// ============================================
// Opção 1: Usando SESSION (Recomendado)
// ============================================

session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    // Redirecionar para login
    header('Location: login.php');
    exit;
}

// Agora você pode usar $userId com segurança
$userId = $_SESSION['user_id'];

// Usar na API:
// require_once __DIR__ . '/app/init.php';
// $resultado = $investimentoController->obterCarteiraUsuario($userId);


// ============================================
// Opção 2: Usando JWT (Token) - Mais Seguro
// ============================================

/*
// Receber o token do header
$headers = getallheaders();
$token = $headers['Authorization'] ?? null;

if (!$token) {
    http_response_code(401);
    die(json_encode(['erro' => 'Token não fornecido']));
}

// Remover "Bearer " do token
$token = str_replace('Bearer ', '', $token);

// Validar e decodificar token (você precisa de uma biblioteca JWT)
// Exemplo com Firebase/JWT:
// use Firebase\JWT\JWT;
// try {
//     $decoded = JWT::decode($token, new Key('sua-chave-secreta', 'HS256'));
//     $userId = $decoded->user_id;
// } catch (Exception $e) {
//     http_response_code(401);
//     die(json_encode(['erro' => 'Token inválido']));
// }
*/


// ============================================
// Opção 3: Modificar api/investimento.php
// ============================================

/*

// No topo de api/investimento.php:

<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/app/init.php';

// === AUTENTICAÇÃO ===
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Usuário não autenticado']);
    exit;
}

$userId = $_SESSION['user_id'];  // Usar user_id da sessão
// Remova a linha: $userId = $_GET['user_id'] ?? 1;

// ... resto do código igual

*/


// ============================================
// Opção 4: Middleware de Autenticação
// ============================================

/*
// Criar um arquivo: app/Middleware/AuthMiddleware.php

<?php

class AuthMiddleware
{
    public static function check()
    {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            die(json_encode(['erro' => 'Não autenticado']));
        }
        
        return $_SESSION['user_id'];
    }
}

// Usar na API:
// require_once __DIR__ . '/app/init.php';
// $userId = AuthMiddleware::check();

*/


// ============================================
// Exemplo de Login Simples
// ============================================

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Gerenciador Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Login - Gerenciador Financeiro</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="processar_login.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
?>
