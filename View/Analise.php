<?php 
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>NOVYX - Análise</title>

    <link rel="stylesheet" href="../template/asset/css/Analise.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>

</head>

<body>

<!-- Código do vLibras -->
<div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper>
        <div class="vw-plugin-top-wrapper"></div>
    </div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
    new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>

    
    <header>
        <nav class="navbar">

            <div class="nav-left">
                <div class="hamburger">
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                </div>
                <a href="#" class="logo">NOVYX</a>
            </div>

            <ul class="nav-links">
                 <li><a href="VisaoGeral.php" >Visão Geral</a></li>
                <li><a href="Investimento.php" >Investimentos</a></li>
                <li><a href="Analise.php" class="active">Análise</a></li>
                <li><a href="Meta.php">Metas</a></li>
                <li><a href="Cartoes.php">Cartões</a></li>
            </ul>

            <div class="user-area">
                <a href="#" class="settings-icon" aria-label="Configurações">
                    <span class="material-symbols-outlined">
                        <i class='bxr  bx-cog'></i>
                    </span>
                </a>
                <img src="" alt="Foto de Perfil" class="profile-pic">
            </div>

        </nav>
    </header>

    <main style="height: 200vh; padding: 100px 2rem; background-color: rgba(249, 249, 249, 1);">
        <div class="container">
            <div class="title">
                <h1>Análise Financeira</h1>
            </div>

            <div class="geral-container">

                <div class="geral-card-renda">
                    <p class="mini">Patrimônio Total</p>
                    <h2>R$45.000</h2>
                    <p class="green"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-arrow-up-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M14 2.5a.5   .5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0z" />
                        </svg>+0% vs mês anterior</p>
                </div>
                <div class="geral-card-metas">
                    <p class="mini">Metas</p>
                    <h2>R$9.200</h2>
                    <p class="green"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-arrow-up-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M14 2.5a.5   .5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0z" />
                        </svg> Faça acontecer. Comece já!</p>
                </div>
                <div class="geral-card-despesas">
                    <p class="mini">Despesas Mensais</p>
                    <h2>R$5.300</h2>
                    <p class="red"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-arrow-down-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M14 13.5a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1 0-1h4.793L2.146 2.854a.5.5 0 1 1 .708-.708L13 12.293V7.5a.5.5 0 0 1 1 0z" />
                        </svg>+0.0% vs mês anterior</p>
                </div>
              
            </div>

            <div class="geral-container">
                <div class="chart-card">
                    <h3>Evolução Patrimonial</h3>
                    <p class="sub-title">Últimos 6 meses</p>
                    <div class="chart-content">
                        <canvas id="evolution-chart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <h3>Despesas por Categoria</h3>
                    <p class="sub-title">Distribuição Mensal</p>
                    <div class="chart-content">
                        <canvas id="expenses-chart"></canvas>
                    </div>
                </div>

            </div>
            <div class="comparativo">
                <h3 class="mensal">Comparativo Mensal</h3>
                <p class="mes"> Maio 2025 vs  Junho 2025</p>

                <div class="comparison-card">
                    <div class="comparison-details">
                        <h3>Patrimônio Total</h3>
                        <p class="prev-value">Anterior: R$ 0</p>
                    </div>
                    <div class="comparison-metrics">
                        <span class="current-value">R$ 9.200</span>
                        <span class="growth green">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-arrow-up-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M14 2.5a.5   .5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0z" />
                            </svg>+0.0%
                        </span>
                    </div>
                </div>

                <div class="comparison-card">
                    <div class="comparison-details">
                        <h3>Despesas Mensais</h3>
                        <p class="prev-value">Anterior: R$ 0</p>
                    </div>
                    <div class="comparison-metrics">
                        <span class="current-value">R$ 4.700</span>
                        <span class="growth red">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-arrow-down-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M14 13.5a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1 0-1h4.793L2.146 2.854a.5.5 0 1 1 .708-.708L13 12.293V7.5a.5.5 0 0 1 1 0z" />
                            </svg>-0.0%
                        </span>
                    </div>
                </div>

                <div class="comparison-card">
                    <div class="comparison-details">
                        <h3>Investimentos</h3>
                        <p class="prev-value">Anterior: R$ 0</p>
                    </div>
                    <div class="comparison-metrics">
                        <span class="current-value">R$ 3.000</span>
                        <span class="growth green">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-arrow-up-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M14 2.5a.5   .5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0z" />
                            </svg>+0.0%
                        </span>
                    </div>
                </div>

             
            </div>
            <div class="recomendacoes-container">
                <div class="title-rec">
                    <h3 class="recomen">Recomendações</h3>
                    <p class="rec">Organize de forma inteligente suas finanças</p>
                </div>

                <div class="recomendacoes-content">

                    <div class="reco-card green-bg">
                        <h3>Planejamento e Orçamento Mensal</h3>
                        <p>Use o método 50-30-20 (50% necessidades, 30% desejos, 20% investimentos/dívidas).</p>
                    </div>

                    <div class="reco-card green-bg">
                        <h3>Metas</h3>
                        <p>Crie metas financeiras!</p>
                    </div>

                    <div class="reco-card yellow-bg">
                        <h3>Atenção às despesas</h3>
                        <p>Analise padrões: onde você gasta mais? há despesas desnecessárias?</p>
                    </div>

                    <div class="reco-card green-bg">
                        <h3>Investimentos</h3>
                        <p>Revise sua carteira periodicamente e reinvista ganhos.</p>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../template/asset/js/Analise.js"></script>
</body>

</html>