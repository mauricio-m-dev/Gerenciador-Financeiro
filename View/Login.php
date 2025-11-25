<?php
/*
|--------------------------------------------------------------------------
| Login.php - Integrado
|--------------------------------------------------------------------------
*/
session_start();

// 1. CARREGAMENTO DOS ARQUIVOS (Ajuste os caminhos se necessário)
// Tenta voltar um diretório para achar o configuration
if (file_exists(__DIR__ . '/../configuration.php')) {
    require_once __DIR__ . '/../configuration.php';
} elseif (file_exists(__DIR__ . '/configuration.php')) {
    require_once __DIR__ . '/configuration.php';
}

// Carrega as classes
// Se estiverem na pasta Model e Controller
if (file_exists(__DIR__ . '/../Model/Connection.php')) {
    require_once __DIR__ . '/../Model/Connection.php';
    require_once __DIR__ . '/../Model/User.php';
    require_once __DIR__ . '/../Controller/UserController.php';
} else {
    // Se estiverem na mesma pasta (fallback)
    require_once 'Connection.php';
    require_once 'User.php';
    require_once 'UserController.php';
}

use Controller\UserController;
use Model\User;

$error_message = '';
$success_message = '';

// Verifica se veio mensagem de sucesso do Cadastro
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $success_message = "Cadastro realizado com sucesso! Faça o login.";
}

// Se o usuário já estiver logado, redireciona
// (Opcional: descomente se quiser impedir que usuário logado veja a tela de login)
/*
if (isset($_SESSION['id'])) {
    header("Location: Dashboard.php");
    exit;
}
*/

// Instancia as classes
$userModel = new User();
$userController = new UserController($userModel);

// 2. PROCESSAMENTO DO FORMULÁRIO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['password'] ?? '');

    if (empty($email) || empty($senha)) {
        $error_message = "Por favor, preencha e-mail e senha.";
    } else {
        
        // Tenta fazer o login
        if ($userController->login($email, $senha)) {
            // SUCESSO: Redireciona para o painel principal
            header("Location: Visaogeral.php");
            exit;
        } else {
            // FALHA
            $error_message = "E-mail ou senha incorretos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" 
        rel="stylesheet"
        crossorigin="anonymous"
    >
    <link 
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" 
        rel="stylesheet"
    >
    <link rel="stylesheet" href="../template/asset/css/Login.css">
    
    <title>Login - NOVYX</title>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">

    <a href="#" class="logo">NOVYX</a>

    <main class="form-container shadow-sm rounded-4 bg-white p-4" style="max-width: 400px; width: 100%;">
        
        <form action="Login.php" method="POST">
            <h2 class="mb-4 text-center fw-semibold minimal-title">Entrar</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger text-center p-2" role="alert">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success text-center p-2" role="alert">
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>

            <div class="form-floating mb-3">
                <input 
                    type="email" 
                    class="form-control minimal-input" 
                    id="floatingInput" 
                    placeholder="seu-email@gmail.com"
                    autocomplete="username"
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
                    autocomplete="current-password"
                    name="password" 
                    required
                >
                <label for="floatingPassword">Senha</label>
                <div class="text-end mt-1">
                    <a href="#" class="text-decoration-none text-muted small">Esqueceu a senha?</a>
                </div>
            </div>
            
            <button class="w-100 btn btn-dark mb-3 p-2" type="submit">
                Entrar
            </button>
            
            <div class="text-center mt-2">
                <span class="text-muted">Não tem uma conta?</span>
                <a href="Cadastro.php" class="text-decoration-none ms-1 fw-bold">Cadastre-se</a>
            </div>
        </form>
    </main>
</body>
</html>