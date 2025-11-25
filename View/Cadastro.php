<?php
/*
|--------------------------------------------------------------------------
| Cadastro.php - Com caminhos corrigidos
|--------------------------------------------------------------------------
*/
session_start();

// __DIR__ pega o caminho atual (View).
// "/../" instrui o código a subir um nível para a pasta principal.

// 1. Carregar Configuração
// Se o seu configuration.php estiver solto na pasta principal:
if (file_exists(__DIR__ . '/../configuration.php')) {
    require_once __DIR__ . '/../configuration.php';
} 
// Se estiver dentro de uma pasta chamada 'Config':
elseif (file_exists(__DIR__ . '/../Config/configuration.php')) {
    require_once __DIR__ . '/../Config/configuration.php';
} 
else {
    die("Erro: Arquivo configuration.php não encontrado. Verifique onde ele está salvo.");
}

// 2. Carregar Models (Conexão e Usuário)
// Verifique se os arquivos estão na pasta 'Model' ou soltos na raiz e ajuste se necessário
if (file_exists(__DIR__ . '/../Model/Connection.php')) {
    require_once __DIR__ . '/../Model/Connection.php';
    require_once __DIR__ . '/../Model/User.php';
} else {
    // Caso estejam soltos na raiz
    require_once __DIR__ . '/../Connection.php';
    require_once __DIR__ . '/../User.php';
}

// 3. Carregar Controller
if (file_exists(__DIR__ . '/../Controller/UserController.php')) {
    require_once __DIR__ . '/../Controller/UserController.php';
} else {
    require_once __DIR__ . '/../UserController.php';
}

use Controller\UserController;
use Model\User;

$error_message = '';

// Instanciação das classes
$userModel = new User();
$userController = new UserController($userModel);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Captura os dados
    $nome = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['password'] ?? '');

    // Validações
    if (empty($nome) || empty($email) || empty($senha)) {
        $error_message = "Por favor, preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Formato de e-mail inválido.";
    } else {
        
        // 1. Verifica se email já existe
        if ($userController->checkUserByEmail($email)) {
            $error_message = "Este e-mail já está cadastrado.";
        } else {
            // 2. Tenta criar o usuário
            if ($userController->createUser($nome, $email, $senha)) {
                // Sucesso: Redireciona para login
                header("Location: Login.php?status=success");
                exit;
            } else {
                $error_message = "Erro ao criar conta no banco de dados.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../template/asset/css/Cadastro.css"> <title>Cadastro - NOVYX</title>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <a href="#" class="logo">NOVYX</a>
    <main class="form-container shadow-sm rounded-4 bg-white p-4" style="max-width: 400px; width: 100%;">
        
        <form action="Cadastro.php" method="POST">
            <h2 class="mb-4 text-center fw-semibold minimal-title">Criar Conta</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger text-center p-2" role="alert">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <div class="form-floating mb-3">
                <input 
                    type="text" 
                    class="form-control minimal-input" 
                    id="floatingName" 
                    placeholder="Seu nome"
                    name="name" 
                    required
                    value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>"
                >
                <label for="floatingName">Nome</label>
            </div>
            
            <div class="form-floating mb-3">
                <input 
                    type="email" 
                    class="form-control minimal-input" 
                    id="floatingInput" 
                    placeholder="seu-email@gmail.com"
                    name="email"
                    required
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                >
                <label for="floatingInput">E-mail</label>
            </div>
            
            <div class="form-floating mb-4">
                <input 
                    type="password" 
                    class="form-control minimal-input" 
                    id="floatingPassword" 
                    placeholder="Senha"
                    name="password"
                    required
                >
                <label for="floatingPassword">Senha</label>
            </div>
            
            <button class="w-100 btn btn-dark mb-3 p-2" type="submit">
                Cadastrar
            </button>
            
            <div class="text-center mt-2">
                <span class="text-muted">Já tem uma conta?</span>
                <a href="Login.php" class="text-decoration-none ms-1 fw-bold">Entrar</a>
            </div>
        </form>
    </main>
</body>
</html>