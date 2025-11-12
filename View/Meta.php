<?php
$data_file = 'metas.json';

// --- FUNÇÕES DE SIMULAÇÃO DE BANCO DE DADOS ---
function load_goals($file)
{
    if (file_exists($file)) {
        $json_data = file_get_contents($file);
        // O true no json_decode retorna um array associativo (objetos em PHP)
        return json_decode($json_data, true) ?: [];
    }
    return [];
}

function save_goals($file, $goals)
{
    // Salva com JSON_PRETTY_PRINT para formatar o arquivo de forma legível
    $json_data = json_encode($goals, JSON_PRETTY_PRINT);
    file_put_contents($file, $json_data);
}

// --- TRATAMENTO DO FORMULÁRIO DO MODAL ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action']) && $_POST['form_action'] === 'add_goal') {

    $goals = load_goals($data_file);

    // Cria um ID simples para a nova meta
    $new_id = str_replace(' ', '', strtolower($_POST['nome']));

    $new_goal = [
        'id' => $new_id,
        'name' => htmlspecialchars($_POST['nome']),
        'objective' => (float) $_POST['objetivo'],
        'monthlyContribution' => (float) $_POST['mensal'],
        'endDate' => $_POST['prazo'],
        'category' => htmlspecialchars($_POST['categoria']),

        // Valores iniciais para nova meta
        'currentSaved' => 0.00,
        'color' => '#38A169', // Cor padrão (verde)
        'dataHistory' => [0.00]
    ];

    $goals[] = $new_goal;
    save_goals($data_file, $goals);

    // Redireciona para evitar re-submissão e recarrega a página com a nova meta
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Carrega a lista final de metas que será usada para renderizar o HTML e o JS
$all_goals = load_goals($data_file);

?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Metas - NOVYX
        
    </title>

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
                <li><a href="VisaoGeral.php" >Visão Geral</a></li>
                <li><a href="Investimento.php">Investimentos</a></li>
                <li><a href="Analise.php">Análise</a></li>
                <li><a href="Metas.php"class="active">Metas</a></li>
                <li><a href="Cartoes.php">Cartões</a></li>
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


    <div id="addMetaModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Adicionar Nova Meta</h2>
                <button id="closeModal" class="close-button">&times;</button>
            </div>

            <form id="newMetaForm">
                <div class="form-group">
                    <label for="meta-nome">Nome da Meta:</label>
                    <input type="text" id="meta-nome" name="nome" required placeholder="Ex: Fundo de Emergência">
                </div>

                <div class="form-group">
                    <label for="meta-objetivo">Valor Objetivo (R$):</label>
                    <input type="number" id="meta-objetivo" name="objetivo" required placeholder="Ex: 12000.00"
                        step="0.01">
                </div>

                <div class="form-group">
                    <label for="meta-mensal">Contribuição Mensal (R$):</label>
                    <input type="number" id="meta-mensal" name="mensal" required placeholder="Ex: 850.00" step="0.01">
                </div>

                <div class="form-group">
                    <label for="meta-prazo">Prazo Final:</label>
                    <input type="date" id="meta-prazo" name="prazo" required>
                </div>

                <div class="form-group">
                    <label for="meta-categoria">Categoria:</label>
                    <select id="meta-categoria" name="categoria">
                        <option value="seguranca">Segurança</option>
                        <option value="lazer">Lazer</option>
                        <option value="moradia">Moradia</option>
                        <option value="veiculo">Veículo</option>
                        <option value="educacao">Educação</option>
                    </select>
                </div>

                <button type="submit" class="submit-button">Criar Meta</button>
            </form>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../template/asset/js/Meta.js"></script>
</body>

</html>