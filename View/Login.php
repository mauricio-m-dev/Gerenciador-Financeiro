<?php
/*
|--------------------------------------------------------------------------
| Lógica de Login
|--------------------------------------------------------------------------
*/
// session_start();
// require_once 'db.php'; // Inclui a conexão com o banco

// // Se o usuário já estiver logado, redireciona para o dashboard
// if (isset($_SESSION['user_id'])) {
//     header("Location: VisaoGeral.php");
//     exit;
// }

// $error_message = ''; // Variável para erros de login
// $success_message = ''; // Variável para sucesso no cadastro

// // Verifica se veio da página de cadastro
// if (isset($_GET['status']) && $_GET['status'] == 'success') {
//     $success_message = "Cadastro realizado com sucesso! Faça o login.";
// }

// // Verifica se o formulário foi submetido
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
//     // 1. Obter dados do formulário
//     $email = trim($_POST['email']);
//     $senha = trim($_POST['password']);

//     // 2. Validação simples
//     if (empty($email) || empty($senha)) {
//         $error_message = "Por favor, preencha e-mail e senha.";
//     } else {
        
//         try {
//             // 3. Buscar o usuário pelo e-mail
//             $sql = "SELECT id, nome, senha FROM usuarios WHERE email = ?";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([$email]);
//             $user = $stmt->fetch();
            
//             // 4. Verificar se o usuário existe E se a senha está correta
//             if ($user && password_verify($senha, $user['senha'])) {
//                 // 5. Sucesso! Armazenar dados na sessão
//                 $_SESSION['user_id'] = $user['id'];
//                 $_SESSION['user_name'] = $user['nome'];
                
//                 // 6. Redirecionar para o dashboard
//                 header("Location: VisaoGeral.php");
//                 exit;
//             } else {
//                 // 7. Falha no login
//                 $error_message = "E-mail ou senha inválidos.";
//             }
//         } catch (PDOException $e) {
//             $error_message = "Erro no banco de dados: " . $e->getMessage();
//         }
//     }
// }
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

<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light ">

    <a href="#" class="logo">NOVYX</a>

    <main class="form-container shadow-sm rounded-4 bg-white p-4">
        
        <form action="Login.php" method="POST">
            <h2 class="mb-4 text-center fw-semibold minimal-title">Entrar</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success" role="alert">
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
                    name="email" required
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
                    name="password" required
                >
                <a href="#" class="forget-password">Esqueceu a senha?</a>
                <label for="floatingPassword">Senha</label>
            </div>
            
            <button class="w-100 btn btn-minimal mb-3" type="submit">
                Entrar
            </button>
            
            <div class="text-center mt-2">
                <span class="text-muted">Não tem uma conta?</span>
                <a href="Cadastro.php" class="cadastro-link ms-1">Cadastre-se</a>
            </div>
        </form>
    </main>
</body>
</html>