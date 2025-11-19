<?php
/*
|--------------------------------------------------------------------------
| A View (VisaoGeralView.php)
|--------------------------------------------------------------------------
| Este é o "Template" (o HTML).
| Ele é chamado pelo Controller (DashboardController).
| Todas as variáveis ($userName, $totalRenda, etc.) vêm do Controller.
*/

/*
|--------------------------------------------------------------------------
| Funções Auxiliares de Formatação (View Helpers)
|--------------------------------------------------------------------------
*/

// Formata um valor numérico para BRL
if (!function_exists('formatCurrency')) {
    function formatCurrency($value)
    {
        if (!is_numeric($value)) {
            return "R$ 0,00";
        }
        return "R$ " . number_format($value, 2, ',', '.');
    }
}

// Formata data do DB (YYYY-MM-DD HH:MM:SS) para (DD Mês YYYY)
if (!function_exists('formatDate')) {
    function formatDate($dateString)
    {
        if (empty($dateString))
            return '';
        $timestamp = strtotime($dateString);
        // Lista de meses em português
        $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        return date('d', $timestamp) . ' ' . $meses[date('n', $timestamp) - 1] . ' ' . date('Y', $timestamp);
    }
}
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
                    <i class='bx bx-cog'></i>
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
                                                    <td class="transaction-date">
                                                        <?= htmlspecialchars(formatDate($tx['data'])) ?></td>

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
                        <div class="view-extrato-mobile-link"
                            style="display: flex; padding: 0 auto; margin-top: -1rem; justify-content: center;">
                            <a href="Extrato.php" class="btn btn-primary">Ver Extrato Completo</a>
                        </div>
                    </div>
                </div>

                <div class="sidebar-area">
                    <div class="category-chart-section">
                        <h2 class="section-title">Despesas por Categoria</h2>
                        <div class="chart-card">
                            <canvas id="expenseDoughnutChart"></canvas>
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
            <form id="modal-form" action="../Controller/AdicionarTransacao.php" method="POST">
                <input type="hidden" id="modal-type" name="tipo">

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
                        <input type="datetime-local" id="modal-data" name="data" required>
                    </div>

                    <div class="form-group">
                        <label for="modal-metodo">Método</label>
                        <select id="modal-metodo" name="metodo" required>
                            <option value="" disabled selected>Selecione</option>
                            <option value="Pix">Pix</option>
                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                            <option value="Cartão de Débito">Cartão de Débito</option>
                            <option value="Boleto">Boleto</option>
                            <option value="Transferência">Transferência</option>
                            <option value="Dinheiro">Dinheiro</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="width: 100%;">
                        <label for="modal-categoria">Categoria</label>
                        <select id="modal-categoria" name="categoria_id" required>
                            <option value="" disabled selected>Selecione o tipo primeiro</option>
                        </select>
                    </div>
                </div>

            </form>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" id="modal-cancel-btn">Cancelar</button>

            <button type="submit" form="modal-form" class="btn btn-primary" id="modal-confirm-btn"
                style="display: block !important; visibility: visible !important; opacity: 1 !important;">Adicionar</button>
        </div>
    </div>

    <script id="app-data" type="application/json">
        {
            "categorias": {
                "renda": <?= json_encode($categoriasRenda) ?>,
                "despesa": <?= json_encode($categoriasDespesa) ?>
            },
            "chartData": {
                "labels": <?= json_encode($categoryData['labels']) ?>,
                "valores": <?= json_encode(array_map('abs', $categoryData['valores'])) ?>
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="../template/asset/js/VisaoGeral.js"></script>


</body>

</html>