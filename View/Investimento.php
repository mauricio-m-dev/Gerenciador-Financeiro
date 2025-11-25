<?php
/*
|--------------------------------------------------------------------------
| View: Investimentos (Design Original + Backend Novo)
|--------------------------------------------------------------------------
*/

session_start();

// 1. Verificação de Segurança
if (!isset($_SESSION['id'])) {
    header('Location: Login.php');
    exit;
}

// 2. Carregamento Robusto (Evita erros de caminho)
$raizInvestimentos = dirname(__DIR__);
require_once $raizInvestimentos . '/config/configuration.php';
require_once $raizInvestimentos . '/Model/Connection.php';
require_once $raizInvestimentos . '/Model/Ativo.php';
require_once $raizInvestimentos . '/Model/Investimento.php';
require_once $raizInvestimentos . '/Controller/InvestimentoController.php';

use Model\Connection;
use Controller\InvestimentoController;

// 3. Inicialização do Controller
try {
    $pdo = Connection::getInstance();
    $controller = new InvestimentoController($pdo);
    $userId = $_SESSION['id'];

    // Processamento de Formulário (Adicionar/Excluir)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
            // Mapeando os nomes do seu form HTML antigo para o Controller novo
            $dadosForm = [
                'ticker' => $_POST['stock_symbol'] ?: $_POST['stock'], // Tenta pegar do hidden, se não, pega do input
                'quantidade' => $_POST['quantidade'],
                'valor' => $_POST['valor_unitario'],
                'tipo_operacao' => 'compra' // O modal original só tinha "Adicionar", assumimos compra
            ];
            $controller->adicionar($dadosForm, $userId);
        }

        // Lógica de exclusão (caso adicione botão de excluir depois)
        if (isset($_POST['acao']) && $_POST['acao'] === 'excluir') {
            $controller->excluir($_POST['id_transacao'], $userId);
        }

        // Refresh para limpar o POST
        header("Location: Investimento.php");
        exit;
    }

    // 4. Busca os Dados (Substitui as chamadas antigas individuais)
    $dados = $controller->index($userId);

    // Mapeia para as variáveis que o seu HTML original espera
    $patrimonio = number_format($dados['patrimonioTotal'], 2, ',', '.');
    $qtdAtivos = $dados['qtdAtivos'];
    $carteira = $dados['carteira'];

    // Prepara JSON para o Gráfico
    $chartLabelsJSON = htmlspecialchars(json_encode($dados['graficoLabels']), ENT_QUOTES, 'UTF-8');
    $chartValoresJSON = htmlspecialchars(json_encode($dados['graficoValores']), ENT_QUOTES, 'UTF-8');

} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}

// Helper de formatação original
function formatCurrency($value)
{
    // Se o valor já vier formatado (string) ou numérico
    if (is_string($value))
        $value = (float) str_replace(['.', ','], ['', '.'], $value);
    return "R$ " . number_format($value, 2, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visao Geral - NOVYX</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>

    <link rel="stylesheet" href="../template/asset/css/Investimento.css">

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
                <a href="VisaoGeral.php" class="logo">NOVYX</a>
            </div>

            <ul class="nav-links">

                <li><a href="VisaoGeral.php">Visão Geral</a></li>
                <li><a href="Investimento.php" class="active">Investimentos</a></li>
                <li><a href="Analise.php">Análise</a></li>
                <li><a href="Meta.php">Metas</a></li>
                <li><a href="CartaoView.php">Cartões</a></li>

            </ul>

            <div class="user-area">
                <a href="#" class="settings-icon" aria-label="Configurações">
                    <span class="material-symbols-outlined">
                        <i class='bxr  bx-cog'></i>
                    </span>
                </a>
            </div>
        </nav>
    </header>

    <main style="height: 100%; padding: 100px 2rem; background-color: #f9f9f9;">


        <div class="container">

            <div class="titulo">
                <h1>Investimentos</h1>

                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                    <i class='bxr bx-plus'></i> Adicionar Investimento
                </button>

                <div class="modal fade" id="staticBackdrop" tabindex="-1" aria-labelledby="staticBackdropLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">Adicionar Investimento</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Fechar"></button>
                            </div>

                            <form id="add-investment-form" method="POST" action="Investimento.php" autocomplete="off">
                                <div class="modal-body">

                                    <input type="hidden" name="acao" value="adicionar">

                                    <div class="mb-3">
                                        <label for="stock-search" class="form-label">Pesquisar ação (Ticker)</label>
                                        <input type="text" class="form-control" id="stock-search" name="stock"
                                            placeholder="Ex: PETR4" required style="text-transform: uppercase;">

                                        <input type="hidden" id="stock-symbol" name="stock_symbol" />
                                        <input type="hidden" id="stock-name" name="stock_name" />

                                        <div id="stock-suggestions" class="autocomplete-list" role="listbox"
                                            aria-label="Sugestões de ações"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="qtd-input" class="form-label">Quantidade de Cotas</label>
                                        <div class="input-group" style="width: 100%;">
                                            <input type="number" step="0.00000001" class="form-control text-center"
                                                id="qtd-input" value="1" min="0.00000001" name="quantidade" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="investment-value-unit" class="form-label">Valor Unitário
                                            (R$)</label>
                                        <input type="number" step="0.01" class="form-control" id="investment-value-unit"
                                            name="valor_unitario" placeholder="0.00" required>
                                        <div class="form-text">Valor pago por unidade.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="investment-value-total" class="form-label">Valor total Estimado
                                            (R$)</label>
                                        <input type="text" class="form-control" id="investment-value-total"
                                            name="valor_total" readonly style="background-color: #e9ecef;">
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>


            <div class="geral-container">
                <div class="geral-card-renda">
                    <p>Patrimônio</p>
                    <h2>R$ <?= $patrimonio ?></h2>
                </div>
                <div class="geral-card-despesas">
                    <p>Valorização</p>
                    <h2 id="valorizacao-valor">R$ 0,00</h2>
                </div>
                <div class="geral-card-metas">
                    <p>Total de ativos</p>
                    <h2><?= $qtdAtivos ?></h2>
                </div>
            </div>


            <div class="mercado">
                <div class="pesquisa">
                    <h2>Mercado de Ações</h2>
                </div>
                <div class="acoes" id="market-cards">
                </div>
            </div>

            <div class="carteira">
                <div class="grafico">
                    <div class="category-chart-section">
                        <h2 class="section-title">Distribuição dos investimentos</h2>
                        <div class="chart-card">
                            <canvas id="expenseDoughnutChart" data-labels='<?= $chartLabelsJSON ?>'
                                data-valores='<?= $chartValoresJSON ?>' style="width:100%;height:350px;display:block;">
                            </canvas>
                        </div>
                    </div>
                </div>


                <div class="seus">
                    <div class="transactions-section">
                        <h2 class="section-title">Histórico de Operações</h2>
                        <div class="card-table-container">
                            <div class="table-scroll-wrapper" style="max-height: 300px;">
                                <table class="transactions-table">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Ativo</th>
                                            <th>Operação</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($dados['historico'])): ?>
                                            <tr>
                                                <td colspan="4" class="empty-table-message">Nenhuma operação registrada.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($dados['historico'] as $hist): ?>
                                                <tr>
                                                    <td class="transaction-date">
                                                        <?= date('d/m/y', strtotime($hist['data_transacao'])) ?>
                                                    </td>
                                                    <td class="transaction-name">
                                                        <strong><?= htmlspecialchars($hist['simbolo']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="<?= $hist['tipo_operacao'] == 'compra' ? 'text-success' : 'text-danger' ?>">
                                                            <?= ucfirst($hist['tipo_operacao']) ?>
                                                        </span>
                                                        <small class="d-block text-muted">
                                                            <?= floatval($hist['quantidade']) ?> x R$
                                                            <?= number_format($hist['valor_unitario'], 2, ',', '.') ?>
                                                        </small>
                                                    </td>
                                                    <td class="align-right">
                                                        <form method="POST" action="Investimento.php"
                                                            onsubmit="return confirm('Tem certeza que deseja apagar este registro?');">
                                                            <input type="hidden" name="acao" value="excluir">
                                                            <input type="hidden" name="id_transacao" value="<?= $hist['id'] ?>">
                                                            <button type="submit" class="btn-delete" title="Excluir">
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
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('expenseDoughnutChart');
            if (ctx) {
                const labels = JSON.parse(ctx.getAttribute('data-labels'));
                const data = JSON.parse(ctx.getAttribute('data-valores'));

                if (labels.length > 0) {
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: ['#2c3e50', '#e74c3c', '#3498db', '#f1c40f', '#9b59b6', '#2ecc71'],
                                borderWidth: 0
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                } else {
                    ctx.style.display = 'none';
                    ctx.parentElement.innerHTML += '<p style="text-align:center; padding-top:50px; color:#888;">Sem dados para exibir</p>';
                }
            }

            // Cálculo simples do total no modal (JS visual)
            const qtdInput = document.getElementById('qtd-input');
            const valorInput = document.getElementById('investment-value-unit');
            const totalInput = document.getElementById('investment-value-total');

            function calcTotal() {
                const q = parseFloat(qtdInput.value) || 0;
                const v = parseFloat(valorInput.value) || 0;
                totalInput.value = (q * v).toFixed(2);
            }
            if (qtdInput && valorInput) {
                qtdInput.addEventListener('input', calcTotal);
                valorInput.addEventListener('input', calcTotal);
            }
        });
    </script>

    <script src="../template/asset/js/Investimento.js"></script>
</body>

</html>