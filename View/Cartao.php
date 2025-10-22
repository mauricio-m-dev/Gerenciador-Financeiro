<?php ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../template/asset/css/Cartao.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>

<body>
    <header class="header">
        <div class="brand">NONYX</div>

        <nav class="nav-links">
            <a href="#" data-index="0" class="active">Visão Geral</a>
            <a href="#" data-index="1">Investimentos</a>
            <a href="#" data-index="2">Análise</a>
            <a href="#" data-index="3">Metas</a>
            <a href="#" data-index="4">Cartões</a>
        </nav>

        <div class="profile-settings">
            <i class="fas fa-cog settings-icon"></i>
            <img src="" alt="Foto de Perfil" class="profile-pic" />
        </div>
    </header>
    <main class="main-content1">
        <h1>Cartões</h1>
        <div class="add-cartao">
            <i class='bx bx-plus'></i>
            <span>Adicionar Cartão</span>
        </div>
        <div class="Minha-carteira">
            <h2> Minhas Carteiras</h2>
            <h5>Selecione uma carteira para ver os detalhes</h5>
            <div class="card-list">
                <div class="card-placeholder">
                    <div class="card-content">
                        <div class="inner-block large"></div>
                        <div class="inner-block small"></div>
                    </div>
                </div>

                <div class="card-placeholder">
                    <div class="card-content">
                        <div class="inner-block large"></div>
                        <div class="inner-block small"></div>
                    </div>
                </div>

                <div class="card-placeholder">
                    <div class="card-content">
                        <div class="inner-block large"></div>
                        <div class="inner-block small"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-content2">
            <div class="Renda mini-summary-card income-card">
                <h4 class="summary-title">Renda</h4>
                <p class="summary-value">R$5.502,45</p>



                </span>
            </div>

            <div class="Despesas mini-summary-card expense-card">
                <h4 class="summary-title">Despesas</h4>
                <p class="summary-value">R$9.450,00</p>



                </span>
            </div>

            <div class="Metas mini-summary-card goal-card">
                <h4 class="summary-title">Metas</h4>
                <p class="summary-value goal-value">R$3.945,55</p>

            </div>
        </div>



        <script src="../template/asset/js/Cartao.js"></script>
</body>

</html>