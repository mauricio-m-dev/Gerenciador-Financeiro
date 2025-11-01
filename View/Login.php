<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" 
        rel="stylesheet"
        crossorigin="anonymous"
    >
    
    <!-- Google Fonts: Inter -->
    <link 
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" 
        rel="stylesheet"
    >
    
    <!-- Estilos Personalizados -->
    <link rel="stylesheet" href="../template/asset/css/Login.css">
    
    <title>Login</title>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light ">

    <a href="#" class="logo">NOVYX</a>

    <main class="form-container shadow-sm rounded-4 bg-white p-4">
        <form>
            <!-- Título -->
            <h2 class="mb-4 text-center fw-semibold minimal-title">Entrar</h2>
            
            <!-- Campo de E-mail -->
            <div class="form-floating mb-3">
                <input 
                    type="email" 
                    class="form-control minimal-input" 
                    id="floatingInput" 
                    placeholder="seu-email@gmail.com"
                    autocomplete="username"
                >
                <label for="floatingInput">E-mail</label>
            </div>
            
            <!-- Campo de Senha -->
            <div class="form-floating mb-4">
                <input 
                    type="password" 
                    class="form-control minimal-input" 
                    id="floatingPassword" 
                    placeholder="Senha"
                    autocomplete="current-password"
                >
                <a href="" class="forget-password">Esqueceu a senha?</a>
                <label for="floatingPassword">Senha</label>
                
            </div>
            
            <!-- Botão de Login -->
            <button class="w-100 btn btn-minimal mb-3" type="submit">
                <a href="VisaoGeral.php">Entrar</a>
            </button>
            
            <!-- Link para Cadastro -->
            <div class="text-center mt-2">
                <span class="text-muted">Não tem uma conta?</span>
                <a href="Cadastro.php" class="cadastro-link ms-1">Cadastre-se</a>
            </div>
        </form>
    </main>
</body>
</html>