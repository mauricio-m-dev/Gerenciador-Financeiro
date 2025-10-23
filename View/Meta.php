<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>gerenciador - testes</title>

    <link rel="stylesheet" href="../template/asset/css/Meta.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

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
                <li><a href="#">Visão Geral</a></li>
                <li><a href="#">Investimentos</a></li>
                <li><a href="#">Análise</a></li>
                <li><a href="#">Metas</a></li>
                <li><a href="#">Cartões</a></li>
            </ul>

            <div class="user-area">
                <a href="#" class="settings-icon" aria-label="Configurações">
                    <i class='bx bx-cog'></i> </a>
                <img src="https://via.placeholder.com/40" alt="Foto de Perfil" class="profile-pic">
            </div>

        </nav>
    </header>
    <main>
        <div class="container">
            <div class="header-meta-section">
                <h1>Metas Financeiras</h1>
                <button class="add-meta-button">
                    <i class='bxr  bxs-plus bx-flip-horizontal'></i>Adicionar Meta</button>
            </div>

            <div class="meta-container">
                <div class="meta-card">
                    <p>Total das Metas</p>
                    <h2>R$ 147.000</h2>
                </div>

                <div class="meta-card valor">
                    <p>Valor Acumulado</p>
                    <h2>R$ 59.700</h2>
                </div>

                <div class="meta-card progresso">
                    <p>Progresso Geral</p>
                    <h2>40.6%</h2>
                </div>

                <div class="meta-card contribuicao">
                    <p>Contribuição Mensal</p>
                    <h2>R$ 5.700</h2>
                </div>
            </div>

            <div class="meta-details">
                <div class="card-details">
                    <div class="card-details-header">
                        <div class="goal-info">
                            <h2>Fundo de Emergência</h2>
                            <p>Segurança</p>
                        </div>
                        <div class="goal-percentage">71%</div>
                    </div>

                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: 71%;"></div>
                    </div>

                    <div class="chart-and-summary">
                        <div class="chart-container">
                            <canvas id="chartEmergencia"></canvas>
                        </div>

                        <div class="summary-details-vertical">
                            <div class="summary-item">
                                <i class='bx bx-dollar'></i> <span>Acumulado: <b>R$ 8.500</b></span>
                            </div>
                            <div class="summary-item">
                                <i class='bx bx-target-lock'></i> <span>Objetivo: <b>R$ 12.000</b></span>
                            </div>
                            <div class="summary-item">
                                <i class='bx bx-trending-up'></i> <span>Mensal: <b style="color: #28a745;">R$
                                        850</b></span>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-details">
                        <div class="timeline-item">
                            <i class='bx bx-calendar'></i> <span>Prazo: Dez 2025</span>
                        </div>
                        <div class="timeline-item timeline-right">
                            <i class='bx bx-time'></i> <span>5 meses</span>
                        </div>
                    </div>

                    <div class="alert-message">
                        <i class='bx bxs-error-circle'></i>
                        <div>
                            <span>Faltam <b>R$ 3.500</b></span>
                            <p>Continue contribuindo para alcançar sua meta!</p>
                        </div>
                    </div>
                </div>

                <div class="card-details">
                    <div class="card-details-header">
                        <div class="goal-info">
                            <h2>Viagem para Europa</h2>
                            <p>Lazer</p>
                        </div>
                        <div class="goal-percentage">28%</div>
                    </div>

                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: 28%;"></div>
                    </div>

                    <div class="chart-and-summary">
                        <div class="chart-container">
                            <canvas id="chartViagem"></canvas>
                        </div>

                        <div class="summary-details-vertical">
                            <div class="summary-item">
                                <i class='bx bx-dollar'></i> <span>Acumulado: <b>R$ 4.200</b></span>
                            </div>
                            <div class="summary-item">
                                <i class='bx bx-target-lock'></i> <span>Objetivo: <b>R$ 15.000</b></span>
                            </div>
                            <div class="summary-item">
                                <i class='bx bx-trending-up'></i> <span>Mensal: <b style="color: #28a745;">R$
                                        600</b></span>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-details">
                        <div class="timeline-item">
                            <i class='bx bx-calendar'></i> <span>Prazo: Jun 2026</span>
                        </div>
                        <div class="timeline-item timeline-right">
                            <i class='bx bx-time'></i> <span>18 meses</span>
                        </div>
                    </div>

                    <div class="alert-message">
                        <i class='bx bxs-error-circle'></i>
                        <div>
                            <span>Faltam <b>R$ 10.800</b></span>
                            <p>Continue contribuindo para alcançar sua meta!</p>
                        </div>
                    </div>
                </div>

                <div class="card-details">
                    <div class="card-details-header">
                        <div class="goal-info">
                            <h2>Entrada do Apartamento</h2>
                            <p>Moradia</p>
                        </div>
                        <div class="goal-percentage">44%</div>
                    </div>

                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: 44%;"></div>
                    </div>

                    <div class="chart-and-summary">
                        <div class="chart-container">
                            <canvas id="chartApartamento"></canvas>
                        </div>

                        <div class="summary-details-vertical">
                            <div class="summary-item">
                                <i class='bx bx-dollar'></i> <span>Acumulado: <b>R$ 35.000</b></span>
                            </div>
                            <div class="summary-item">
                                <i class='bx bx-target-lock'></i> <span>Objetivo: <b>R$ 80.000</b></span>
                            </div>
                            <div class="summary-item">
                                <i class='bx bx-trending-up'></i> <span>Mensal: <b style="color: #28a745;">R$
                                        2.500</b></span>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-details">
                        <div class="timeline-item">
                            <i class='bx bx-calendar'></i> <span>Prazo: Jan 2027</span>
                        </div>
                        <div class="timeline-item timeline-right">
                            <i class='bx bx-time'></i> <span>18 meses</span>
                        </div>
                    </div>

                    <div class="alert-message">
                        <i class='bx bxs-error-circle'></i>
                        <div>
                            <span>Faltam <b>R$ 45.800</b></span>
                            <p>Continue contribuindo para alcançar sua meta!</p>
                        </div>
                    </div>
                </div>

                <div class="card-details">
                    <div class="card-details-header">
                        <div class="goal-info">
                            <h2>Novo Carro</h2>
                            <p>Veículo</p>
                        </div>
                        <div class="goal-percentage">30%</div>
                    </div>

                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: 30%;"></div>
                    </div>

                    <div class="chart-and-summary">
                        <div class="chart-container">
                            <canvas id="chartCarro"></canvas>
                        </div>

                        <div class="summary-details-vertical">
                            <div class="summary-item">
                                <i class='bx bx-dollar'></i> <span>Acumulado: <b>R$ 12.000</b></span>
                            </div>
                            <div class="summary-item">
                                <i class='bx bx-target-lock'></i> <span>Objetivo: <b>R$ 40.000</b></span>
                            </div>
                            <div class="summary-item">
                                <i class='bx bx-trending-up'></i> <span>Mensal: <b style="color: #28a745;">R$
                                        1.750</b></span>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-details">
                        <div class="timeline-item">
                            <i class='bx bx-calendar'></i> <span>Prazo: Set 2026</span>
                        </div>
                        <div class="timeline-item timeline-right">
                            <i class='bx bx-time'></i> <span>16 meses</span>
                        </div>
                    </div>

                    <div class="alert-message">
                        <i class='bx bxs-error-circle'></i>
                        <div>
                            <span>Faltam <b>R$ 28.800</b></span>
                            <p>Continue contribuindo para alcançar sua meta!</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tips-section">
                <h2>Dicas Personalizadas</h2>
                <p>Recomendações para alcançar suas metas mais rápido</p>

                <div class="tip-card tip-success">
                    <span class="tip-title-success">• Excelente taxa de poupança!</span>
                    <p>Você está economizando 42.4% da sua renda, acima da média recomendada de 30%.</p>
                </div>

                <div class="tip-card tip-info">
                    <span class="tip-title-info">• Aumente a contribuição</span>
                    <p>Aumentando +R$ 200/mês na viagem, você alcança a meta 3 meses antes.</p>
                </div>

                <div class="tip-card tip-info">
                    <span class="tip-title-info">• Considere investir</span>
                    <p>Investindo suas contribuições à 10% a.a., você pode acelerar suas metas.</p>
                </div>
            </div>
        </div>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../template/asset/js/Meta.js"></script>
</body>

</html>