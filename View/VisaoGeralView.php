<?php
/*
|--------------------------------------------------------------------------
| A View (VisaoGeralView.php)
|--------------------------------------------------------------------------
*/

// Helpers (Mantidos aqui por enquanto, mas idealmente mover para classe separada)
if (!function_exists('formatCurrency')) {
    function formatCurrency($value)
    {
        if (!is_numeric($value))
            return "R$ 0,00";
        return "R$ " . number_format($value, 2, ',', '.');
    }
}

if (!function_exists('formatDate')) {
    function formatDate($dateString)
    {
        if (empty($dateString))
            return '';
        $timestamp = strtotime($dateString);
        $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        return date('d', $timestamp) . ' ' . $meses[date('n', $timestamp) - 1] . ' ' . date('Y', $timestamp);
    }
}

require_once '../Controller/MetaController.php';

// --- CORREÇÃO AQUI ---
// 1. Verifica se a função getConexao existe, se não, inclui o arquivo de config
if (!function_exists('getConexao')) {
    // Ajuste o caminho '../Config/configuration.php' se necessário
    // Se o seu arquivo se chama db.php, mude aqui.
    if (file_exists('../Config/configuration.php')) {
        require_once '../Config/configuration.php';
    } elseif (file_exists('../db.php')) {
        require_once '../db.php';
    }
}

// 2. Cria a variável $pdo explicitamente chamando a função
$pdo = getConexao();

// 3. Agora sim instanciamos o controller passando a conexão válida
if (!isset($metaController)) {
    $metaController = new MetaController($pdo);
}

// 4. Define o userId (deve vir da sessão ou data do controller)
$userId = $_SESSION['id'] ?? null;

// 5. Busca as metas do usuário logado para preencher o select
$listaMetas = !empty($userId) ? $metaController->index($userId) : [];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visão Geral - NOVYX</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../template/asset/css/VisaoGeral.css">

    <style>
        .btn-delete-icon {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            color: #888;
            padding: 5px;
            border-radius: 4px;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-delete-icon:hover {
            color: #e63946;
            background-color: rgba(230, 57, 70, 0.1);
        }

        .align-center {
            text-align: center;
        }
    </style>
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
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
                <a href="#" class="logo">NOVYX</a>
            </div>
            <ul class="nav-links">
                <li><a href="VisaoGeral.php" class="active">Visão Geral</a></li>
                <li><a href="Investimento.php">Investimentos</a></li>
                <li><a href="Analise.php">Análise</a></li>
                <li><a href="Meta.php">Metas</a></li>
                <li><a href="Cartao.php">Cartões</a></li>
            </ul>
            <div class="user-area">
                <a href="#" class="settings-icon"><i class='bx bx-cog'></i></a>
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
                        <p>Crie uma despesa manualmente</p>
                    </button>
                </div>
                <div class="geral-button-metas">
                    <button id="btn-add-metas">
                        <h2>Adicionar renda - Metas</h2>
                        <p>Selecione o valor e adicione</p>
                    </button>
                </div>
            </div>

            <div class="mobile-action-button-container">
                <button id="btn-add-unified" class="btn-float-add">
                    <i class='bx bx-plus bx-add'></i><span>Adicionar</span>
                </button>
            </div>

            <div class="dashboard-grid">
                <div class="main-content-area">
                    <div class="transactions-section">
                        <h2 class="section-title section-title-transactions">Últimas Transações</h2>
                        <div class="card-table-container" style="@media screen and (max-width: 768px) {.card-table-container,
                .section-title-transactions {
                    display: none !important;
                }}">
                            <div class="table-scroll-wrapper">

                                <table class="transactions-table">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Método</th>
                                            <th>Data</th>
                                            <th class="align-right">Quantia</th>
                                            <th class="align-center" style="width: 50px;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($transactions)): ?>
                                            <tr>
                                                <td colspan="5" class="empty-table-message">Nenhuma transação encontrada.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($transactions as $tx): ?>
                                                <tr>
                                                    <td class="transaction-name"><?= htmlspecialchars($tx['nome']) ?></td>
                                                    <td class="transaction-method"><?= htmlspecialchars($tx['metodo']) ?></td>
                                                    <td class="transaction-date">
                                                        <?= htmlspecialchars(formatDate($tx['data'])) ?>
                                                    </td>

                                                    <?php
                                                    $amountClass = ($tx['tipo'] === 'renda') ? 'amount-income' : 'amount-expense';
                                                    $prefix = ($tx['tipo'] === 'renda') ? '+ ' : '- ';
                                                    ?>

                                                    <td class="<?= $amountClass ?> align-right">
                                                        <?= $prefix . formatCurrency(abs($tx['quantia'])) ?>
                                                    </td>

                                                    <td class="align-center">
                                                        <form action="../Controller/ExcluirTransacao.php" method="POST"
                                                            onsubmit="return confirm('Excluir esta transação?');">
                                                            <input type="hidden" name="id_transacao" value="<?= $tx['id'] ?>">
                                                            <button type="submit" class="btn-delete-icon" aria-label="Excluir">
                                                                <i class='bx bx-trash'></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="view-extrato-mobile-link"
                            style="display: flex; margin-top: -1rem; justify-content: center;">
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
    </main>

    <div class="chooser-overlay" id="chooser-overlay"></div>
    <div class="chooser-modal" id="action-chooser-modal">
        <div class="chooser-header">
            <h2>O que deseja adicionar?</h2>
            <button class="modal-close-btn" id="chooser-close-btn">&times;</button>
        </div>
        <div class="chooser-body">
            <button class="chooser-button" id="chooser-btn-renda"><i
                    class='bx bx-trending-up'></i><span>Renda</span></button>
            <button class="chooser-button" id="chooser-btn-despesa"><i
                    class='bx bx-trending-down'></i><span>Despesa</span></button>
            <button class="chooser-button" id="chooser-btn-metas"><i
                    class='bx bx-target-lock'></i><span>Meta</span></button>
        </div>
    </div>

    <div class="modal-overlay" id="modal-overlay"></div>
    <div class="transaction-modal" id="transaction-modal">
        <div class="modal-header">
            <h2 id="modal-title">Adicionar</h2>
            <button class="modal-close-btn" id="modal-close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <form id="modal-form" action="../Controller/AdicionarTransacao.php" method="POST">
                <input type="hidden" id="modal-type" name="tipo">
                <div class="form-group">
                    <label for="modal-nome">Nome</label>
                    <input type="text" id="modal-nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="modal-quantia">Quantia</label>
                    <input type="number" id="modal-quantia" name="quantia" step="0.01" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="modal-data">Data</label>
                        <input type="datetime-local" id="modal-data" name="data" required>
                    </div>
                    <div class="form-group">
                        <label for="modal-metodo">Método</label>
                        <select id="modal-metodo" name="metodo" required>
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
                        <select id="modal-categoria" name="categoria_id" required></select>
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

    <div class="transaction-modal" id="modal-meta-aporte" style="display: none;">

        <div class="modal-header modal-header-metas">
            <h2>Adicionar à Meta</h2>
            <button class="modal-close-btn" id="close-meta-modal">&times;</button>
        </div>

        <div class="modal-body">
            <form id="form-meta-aporte">

                <div class="form-group">
                    <label for="select-meta-id">Escolha a Meta</label>
                    <select id="select-meta-id" name="meta_id" required>
                        <option value="" disabled selected>Selecione...</option>
                        <?php if (!empty($listaMetas)): ?>
                            <?php foreach ($listaMetas as $m): ?>
                                <option value="<?= $m['id'] ?>">
                                    <?= htmlspecialchars($m['nome']) ?> (Meta: R$
                                    <?= number_format($m['objetivo'], 2, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>Nenhuma meta cadastrada</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="meta-valor">Valor do Aporte (R$)</label>
                    <input type="number" id="meta-valor" name="amount" step="0.01" required placeholder="Ex: 100.00">
                </div>

                <div class="form-group">
                    <label for="meta-data">Data</label>
                    <input type="month" id="meta-data" name="date" required value="<?= date('Y-m') ?>">
                </div>

                <div class="modal-footer-metas">
                    <button type="button" class="btn btn-secondary" id="cancel-meta-btn">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar Aporte</button>
                </div>

            </form>
        </div>
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