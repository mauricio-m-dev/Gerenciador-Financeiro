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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    

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
               <div class="inner-block small2 increase-indicator">
    <i class='bx bx-up-arrow-alt'></i> 12,5%
</div>



                </span>
            </div>

            <div class="Despesas mini-summary-card expense-card">
                <h4 class="summary-title">Despesas</h4>
                <p class="summary-value">R$9.450,00</p>
                <div class="inner-block small2 increase-indicator">
    <i class='bx bx-up-arrow-alt'></i> 27,1%
</div>


                </span>
            </div>

            <div class="Metas mini-summary-card goal-card">
                <h4 class="summary-title">Metas</h4>
                <p class="summary-value goal-value">R$3.945,55</p>
               <div class="inner-block small2 decrease-indicator">
    <i class='bx bx-down-arrow-alt'></i> -15% 
</div>

            </div>
        </div>
        <div class="main-content3">
    
   <div class="chart-section">
    <h2>Despesas por categoria</h2>
    <div class="donut-chart-placeholder"></div>
    
    <ul class="category-list">
        <li class="category-item">
            <span class="category-icon-wrapper dot-casa">
                <i class='bx bx-home-alt'></i> 
            </span> 
            Casa 
            <span class="percentage">41,35%</span>
        </li>
        
        <li class="category-item">
            <span class="category-icon-wrapper dot-cartao">
                <i class='bx bx-credit-card-alt'></i> 
            </span> 
            Cartão de crédito 
            <span class="percentage">21,51%</span>
        </li>
        
        <li class="category-item">
            <span class="category-icon-wrapper dot-transporte">
                <i class='bx bx-car'></i> 
            </span> 
            Transporte 
            <span class="percentage">13,47%</span>
        </li>
        
        <li class="category-item">
            <span class="category-icon-wrapper dot-mantimentos">
                <i class='bx bx-lemon'></i> 
            </span> 
            Mantimentos 
            <span class="percentage">9,97%</span>
        </li>
        
        <li class="category-item">
            <span class="category-icon-wrapper dot-compras">
                <i class='bx bx-shopping-bag'></i> 
            </span> 
            Compras 
            <span class="percentage">3,35%</span>
        </li>
    </ul>
</div>
    
    <div class="transactions-section">
        <h2>Últimas transações</h2>
        <p class="section-subtitle">Verifique suas últimas transações</p>
        
        <table class="transactions-table">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Método</th>
                    <th>Data</th>
                    <th>Quantia</th>
                    <th></th> </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="initials orlando">OR</span> Orlando Rodrigues</td>
                    <td>Conta Bancária</td>
                    <td>2024/04/01</td>
                    <td class="income">+R$750,00</td>
                    <td><i class='bx bx-dots-vertical-rounded'></i></td>
                </tr>
                <tr>
                    <td><span class="initials netflix">N</span> Netflix</td>
                    <td>Cartão de crédito</td>
                    <td>2024/03/29</td>
                    <td class="expense">-R$9,90</td>
                    <td><i class='bx bx-dots-vertical-rounded'></i></td>
                </tr>
                </tbody>
        </table>

       
        </div>
        
    </div>
</div>

    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="../template/asset/js/Cartao.js"></script>
</body>

</html>