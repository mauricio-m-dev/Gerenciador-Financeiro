<?php
/*
|--------------------------------------------------------------------------
| Lógica de Cadastro
|--------------------------------------------------------------------------
*/
// session_start();
// require_once 'db.php'; // Inclui a conexão com o banco

// $error_message = ''; // Variável para armazenar mensagens de erro

// // Verifica se o formulário foi submetido
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
//     // 1. Obter e limpar dados do formulário
//     $nome = trim($_POST['nome']);
//     $email = trim($_POST['email']);
//     $senha = trim($_POST['password']);

//     // 2. Validação simples
//     if (empty($nome) || empty($email) || empty($senha)) {
//         $error_message = "Por favor, preencha todos os campos.";
//     } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//         $error_message = "Formato de e-mail inválido.";
//     } else {
        
//         try {
//             // 3. Verificar se o e-mail já existe
//             $sql = "SELECT id FROM usuarios WHERE email = ?";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([$email]);
            
//             if ($stmt->rowCount() > 0) {
//                 $error_message = "Este e-mail já está cadastrado.";
//             } else {
//                 // 4. Se não existe, criar o usuário
                
//                 // Criptografa a senha (NUNCA armazene senhas em texto puro)
//                 $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                
//                 // 5. Inserir no banco de dados
//                 $sql_insert = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
//                 $stmt_insert = $pdo->prepare($sql_insert);
                
//                 if ($stmt_insert->execute([$nome, $email, $senha_hash])) {
//                     // 6. Redirecionar para o login com mensagem de sucesso
//                     header("Location: Login.php?status=success");
//                     exit;
//                 } else {
//                     $error_message = "Erro ao criar a conta. Tente novamente.";
//                 }
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
    
    <link rel="stylesheet" href="../template/asset/css/Cadastro.css">
    
    <title>Cadastro - NOVYX</title>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <a href="#" class="logo">NOVYX</a>
    <main class="form-container shadow-sm rounded-4 bg-white p-4">
        
        <form action="Cadastro.php" method="POST">
            <h2 class="mb-4 text-center fw-semibold minimal-title">Criar Conta</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <div class="form-floating mb-3">
                <input 
                    type="text" 
                    class="form-control minimal-input" 
                    id="floatingNome" 
                    placeholder="Seu nome"
                    autocomplete="name"
                    name="nome"
                    required
                >
                <label for="floatingNome">Nome</label>
            </div>
            
            <div class="form-floating mb-3">
                <input 
                    type="email" 
                    class="form-control minimal-input" 
                    id="floatingInput" 
                    placeholder="seu-email@gmail.com"
                    autocomplete="username"
                    name="email"
                    required
                >
                <label for="floatingInput">E-mail</label>
            </div>
            
            <div class="form-floating mb-4">
                <input 
                    type="password" 
                    class="form-control minimal-input" 
                    id="floatingPassword" 
                    placeholder="Senha"
                    autocomplete="new-password"
                    name="password"
                    required
                >
                <label for="floatingPassword">Senha</label>
            </div>
            
            <button class="w-100 btn btn-minimal mb-3" type="submit">
                Cadastrar
            </button>
            
            <div class="text-center mt-2">
                <span class="text-muted">Já tem uma conta?</span>
                <a href="Login.php" class="cadastro-link ms-1">Entrar</a>
            </div>
        </form>
    </main>
</body>
</html>