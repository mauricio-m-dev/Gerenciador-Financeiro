<?php
/*
|--------------------------------------------------------------------------
| SIMULAÇÃO DE DADOS DO BACKEND
|--------------------------------------------------------------------------
| Estes dados viriam do seu banco de dados e lógica de sessão.
*/

// 1. Dados do Usuário (Sessão)
$userName = "Mauricio";
$userProfilePic = "https://via.placeholder.com/40";

// 2. Dados dos Cards Principais (Consulta SQL)
$totalRenda = 147000.00;
$totalDespesas = 59700.00;
$totalMetas = 45909.00;

// 3. Dados da Tabela de Transações (Consulta SQL com LIMIT)
$transactions = [
    [
        "nome" => "Spotify",
        "metodo" => "Cartão de Débito",
        "data" => "21 Out 2025",
        "quantia" => -29.90,
        "tipo" => "despesa"
    ],
    [
        "nome" => "Freelance Projeto Y",
        "metodo" => "Pix",
        "data" => "18 Out 2025",
        "quantia" => 2300.00,
        "tipo" => "renda"
    ],
    [
        "nome" => "Aluguel",
        "metodo" => "Boleto",
        "data" => "10 Out 2025",
        "quantia" => -1200.00,
        "tipo" => "despesa"
    ],
    [
        "nome" => "Supermercado",
        "metodo" => "Cartão de Crédito",
        "data" => "20 Out 2025",
        "quantia" => -350.75,
        "tipo" => "despesa"
    ],
    [
        "nome" => "Salário Empresa X",
        "metodo" => "Pix",
        "data" => "19 Out 2025",
        "quantia" => 4500.00,
        "tipo" => "renda"
    ]
];

// 4. Dados do Gráfico (Consulta SQL com GROUP BY)
$categoryData = [
    "labels" => ["Alimentação", "Moradia", "Transporte", "Lazer", "Outros"],
    "valores" => [850.50, 1200.00, 350.20, 400.00, 150.75]
];

/*
|--------------------------------------------------------------------------
| Funções Auxiliares de Formatação
|--------------------------------------------------------------------------
*/

// Formata um valor numérico para BRL
function formatCurrency($value)
{
    return "R$ " . number_format($value, 2, ',', '.');
}

// Prepara os dados do gráfico para passar ao JS
$chartLabelsJSON = htmlspecialchars(json_encode($categoryData['labels']), ENT_QUOTES, 'UTF-8');
$chartValoresJSON = htmlspecialchars(json_encode($categoryData['valores']), ENT_QUOTES, 'UTF-8');

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visao Geral - NOVYX</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <link rel="stylesheet" href="../template/asset/css/VisaoGeral.css">
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
                <li><a href="VisaoGeral.php" class="active">Visão Geral</a></li>
                <li><a href="#">Investimentos</a></li>
                <li><a href="#">Análise</a></li>
                <li><a href="#">Metas</a></li>
                <li><a href="#">Cartões</a></li>
            </ul>

            <div class="user-area">
                <a href="#" class="settings-icon" aria-label="Configurações">
                    <span class="material-symbols-outlined">
                        <i class='bx bx-cog'></i>
                    </span>
                </a>
                <img src="<?= htmlspecialchars($userProfilePic) ?>"
                    alt="Foto de Perfil de <?= htmlspecialchars($userName) ?>" class="profile-pic">
            </div>
        </nav>
    </header>

    <main>

        <div class="container">
            <h1>Olá, <?= htmlspecialchars($userName) ?></h1>

            <div class="geral-container">
                <div class="geral-card">
                    <p>Renda</p>
                    <h2 class="h2-renda"><?= formatCurrency($totalRenda) ?></h2>
                </div>

                <div class="geral-card">
                    <p>Despesas</p>
                    <h2 class="h2-despesas"><?= formatCurrency($totalDespesas) ?></h2>
                </div>

                <div class="geral-card">
                    <p>Metas</p>
                    <h2 class="h2-metas"><?= formatCurrency($totalMetas) ?></h2>
                </div>
            </div>
        </div>

        <div class="container">

            <div class="geral-container desktop-action-buttons">
                <div class="geral-button-renda">
                    <button id="btn-add-renda">
                        <h2>Adicionar renda</h2>
                        <p>Crie uma renda manualmente</p>
                    </button>
                </div>
                <div class="geral-button-despesas">
                    <button id="btn-add-despesa">
                        <h2>Adicionar despesa</h2>
                        <p>Crie uma despesa manually</p>
                    </button>
                </div>
                <div class="geral-button-metas">
                    <button id="btn-add-metas">
                        <h2>Adicionar renda - Metas</h2>
                        <p>Selecione o valor e adicione á metas</p>
                    </button>
                </div>
            </div>

            <div class="mobile-action-button-container">
                <button id="btn-add-unified" class="btn-float-add">
                    <i class='bx bx-plus'></i>
                    <span>Adicionar</span>
                </button>
            </div>

            <div class="dashboard-grid">

                <div class="main-content-area">
                    <div class="transactions-section">
                        <h2 class="section-title section-title-transactions">Últimas Transações</h2>
                        <div class="card-table-container">
                            <div class="table-scroll-wrapper">
                                <table class="transactions-table">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Método</th>
                                            <th>Data</th>
                                            <th class="align-right">Quantia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($transactions)): ?>
                                            <tr>
                                                <td colspan="4" class="empty-table-message">
                                                    Nenhuma transação encontrada.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($transactions as $tx): ?>
                                                <tr>
                                                    <td class="transaction-name"><?= htmlspecialchars($tx['nome']) ?></td>
                                                    <td class="transaction-method"><?= htmlspecialchars($tx['metodo']) ?></td>
                                                    <td class="transaction-date"><?= htmlspecialchars($tx['data']) ?></td>

                                                    <?php
                                                    $amountClass = ($tx['tipo'] === 'renda') ? 'amount-income' : 'amount-expense';
                                                    $prefix = ($tx['tipo'] === 'renda') ? '+ ' : '- ';
                                                    ?>
                                                    <td class="<?= $amountClass ?> align-right">
                                                        <?= $prefix . formatCurrency(abs($tx['quantia'])) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="view-extrato-mobile-link" style="display: flex; padding: 0 auto; margin-top: -1rem; justify-content: center;">
                            <a href="Extrato.php" class="btn btn-primary">Ver Extrato Completo</a>
                        </div>
                    </div>
                </div>

                <div class="sidebar-area">
                    <div class="category-chart-section">
                        <h2 class="section-title">Despesas por Categoria</h2>
                        <div class="chart-card">
                            <canvas id="expenseDoughnutChart" data-labels='<?= $chartLabelsJSON ?>'
                                data-valores='<?= $chartValoresJSON ?>'></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>

    <div class="chooser-overlay" id="chooser-overlay"></div>
    <div class="chooser-modal" id="action-chooser-modal">
        <div class="chooser-header">
            <h2>O que deseja adicionar?</h2>
            <button class="modal-close-btn" id="chooser-close-btn" aria-label="Fechar modal">&times;</button>
        </div>
        <div class="chooser-body">
            <button class="chooser-button" id="chooser-btn-renda">
                <i class='bx bx-trending-up'></i>
                <span>Adicionar Renda</span>
            </button>
            <button class="chooser-button" id="chooser-btn-despesa">
                <i class='bx bx-trending-down'></i>
                <span>Adicionar Despesa</span>
            </button>
            <button class="chooser-button" id="chooser-btn-metas">
                <i class='bx bx-target-lock'></i>
                <span>Adicionar à Meta</span>
            </button>
        </div>
    </div>

    <div class="modal-overlay" id="modal-overlay"></div>
    <div class="transaction-modal" id="transaction-modal">
        <div class="modal-header">
            <h2 id="modal-title">Adicionar Título</h2>
            <button class="modal-close-btn" id="modal-close-btn" aria-label="Fechar modal">&times;</button>
        </div>

        <div class="modal-body">
            <form id="modal-form">
                <input type="hidden" id="modal-type" name="type">

                <div class="form-group">
                    <label for="modal-nome">Nome</label>
                    <input type="text" id="modal-nome" name="nome" placeholder="Ex: Salário, Aluguel" required>
                </div>

                <div class="form-group">
                    <label for="modal-quantia">Quantia</label>
                    <input type="number" id="modal-quantia" name="quantia" placeholder="R$ 0,00" step="0.01" min="0.01"
                        required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="modal-data">Data</label>
                        <input type="date" id="modal-data" name="data" required>
                    </div>

                    <div class="form-group">
                        <label for="modal-metodo">Método</label>
                        <select id="modal-metodo" name="metodo" required>
                            <option value="" disabled selected>Selecione</option>
                            <option value="pix">Pix</option>
                            <option value="cartao_credito">Cartão de Crédito</option>
                            <option value="cartao_debito">Cartão de Débito</option>
                            <option value="boleto">Boleto</option>
                            <option value="transferencia">Transferência</option>
                            <option value="dinheiro">Dinheiro</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" id="modal-cancel-btn">Cancelar</button>
            <button type="submit" form="modal-form" class="btn btn-primary" id="modal-confirm-btn">Confirmar</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="../template/asset/js/VisaoGeral.js"></script>

</body>

</html>