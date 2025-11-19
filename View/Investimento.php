<?php
/**
 * View: Investimento.php
 * Página de investimentos integrada com o banco de dados via MVC
 */

// Carrega o arquivo de inicialização
require_once __DIR__ . '/../app/init.php';

// IMPORTANTE: Obtenha o user_id do usuário logado
// Por enquanto, usamos 1 para testes. Você deve integrar com seu sistema de autenticação.
$userId = $_GET['user_id'] ?? 1;

// Obtém estatísticas da carteira
$estatisticas = $investimentoController->calcularEstatisticas($userId);
$patrimonio = number_format($estatisticas['patrimonio_total'], 2, ',', '.');
$qtdAtivos = $estatisticas['qtd_ativos'];

// Obtém o histórico de transações
$historico = $investimentoController->obterHistoricoTransacoes($userId);

// Obtém a carteira para dados do gráfico
$carteira = $investimentoController->obterCarteiraUsuario($userId);

// Prepara dados para o gráfico com base na carteira real
$chartLabels = [];
$chartValores = [];

if (!empty($carteira)) {
    foreach ($carteira as $ativo) {
        // Usa apenas o ticker (asset_symbol)
        $chartLabels[] = $ativo['asset_symbol'];
        // Usa o valor investido
        $chartValores[] = (float)$ativo['valor_investido'];
    }
} else {
    // Se não há ativos, exibe um gráfico vazio
    $chartLabels = [];
    $chartValores = [];
}

function formatCurrency($value)
{
    return "R$ " . number_format($value, 2, ',', '.');
}

$chartLabelsJSON = htmlspecialchars(json_encode($chartLabels), ENT_QUOTES, 'UTF-8');
$chartValoresJSON = htmlspecialchars(json_encode($chartValores), ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visao Geral - NOVYX</title>

    <!-- Bootstrap CSS primeiro -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fontes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>

    <!-- Seu CSS por último (para sobrescrever o Bootstrap se necessário) -->
    <link rel="stylesheet" href="../template/asset/css/Investimento.css">

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
                <li><a href="#" class="active">Investimentos</a></li>
                <li><a href="#">Análise</a></li>
                <li><a href="#">Metas</a></li>
                <li><a href="#">Cartões</a></li>

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

    <main style="height: 100%; padding: 100px 2rem; background-color: #f9f9f9;">


        <div class="container">

            <div class="titulo">
                <h1>Investimentos</h1>

                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                    <i class='bxr bx-plus'></i> Adicionar Investimento
                </button>

                <!-- Modal Bootstrap correto (id = staticBackdrop) -->
                <div class="modal fade" id="staticBackdrop" tabindex="-1" aria-labelledby="staticBackdropLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">Adicionar Investimento</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Fechar"></button>
                            </div>
                            <div class="modal-body">
                                <!-- formulário / conteúdo do modal -->
                                <form id="add-investment-form" autocomplete="off">

                                    <div class="mb-3">
                                        <label for="stock-search" class="form-label">Pesquisar ação</label>
                                        <input type="text" class="form-control" id="stock-search" name="stock"
                                            placeholder="Ex: PETR4 ou Petrobras" aria-autocomplete="list"
                                            aria-controls="stock-suggestions" aria-expanded="false" />
                                        <input type="hidden" id="stock-symbol" name="stock_symbol" />
                                        <input type="hidden" id="stock-name" name="stock_name" />
                                        <div id="stock-suggestions" class="autocomplete-list" role="listbox"
                                            aria-label="Sugestões de ações"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="qtd-input" class="form-label">Quantidade de Cotas</label>
                                        <div class="input-group" style="width: 100%;">


                                            <input type="text" class="form-control text-center" id="qtd-input" value="1"
                                                min="1" name="quantidade">

                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="investment-value-unit" class="form-label">Valor Unitário
                                            (R$)</label>
                                        <input type="text" class="form-control" id="investment-value-unit"
                                            name="valor_unitario" value="0.00">
                                        <div class="form-text">Valor atual da ação, carregado da API.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="investment-value-unit" class="form-label">Valor total
                                            (R$)</label>
                                        <input type="text" class="form-control" id="investment-value-total"
                                            name="valor_total" value="0.00" readonly>
                                        <div class="form-text">Valor total da ação, carregado da API.</div>
                                    </div>

                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-primary">Salvar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>




            <div class="geral-container">
                <div class="geral-card-renda">
                    <p>Patrimônio</p>
                    <h2>R$ 156.000</h2>
                </div>
                <div class="geral-card-despesas">
                    <p>Valorização</p>
                    <h2>R$ 10.000 </h2>
                </div>
                <div class="geral-card-metas">
                    <p>Total de ativos</p>
                    <h2><?= $qtdAtivos ?></h2>
                </div>
            </div>


            <div class="mercado">
                <div class="pesquisa">
                    <h2>Mercado de Ações</h2>

                    <!-- Barra de pesquisa com o autocomplete -->
                    <div class="barra">
                        <form class="d-flex" role="search">
                            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"
                                autocomplete="off" id="pesquisa-mercado">
                            <button class="btn btn-outline-success" type="submit">Search</button>
                        </form>
                        <div class="result-box">

                        </div>
                    </div>

                </div>

                    <div class="acoes" id="market-cards">
                        <!-- Market cards will be rendered here by JavaScript (10 stocks from BRAPI) -->
                    </div>


            </div>

            <div class="carteira">
                <div class="grafico">
                    <div class="category-chart-section">
                        <h2 class="section-title">Distribuição dos investimentos</h2>
                        <div class="chart-card">
                            <canvas id="expenseDoughnutChart" data-labels='<?= $chartLabelsJSON ?>'
                                data-valores='<?= $chartValoresJSON ?>' style="width:100%;height:350px;display:block;"></canvas>
                        </div>
                    </div>
                </div>


                <div class="seus">
                    <div class="transactions-section">
                        <h2 class="section-title section-title-transactions">Seus ativos</h2>
                        <div class="card-table-container">
                            <div class="table-scroll-wrapper">
                                <table class="transactions-table">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th class="align-right">Cotas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Obtém o histórico de transações para ter acesso aos IDs
                                        $historico = $investimentoController->obterHistoricoTransacoes($userId);
                                        $carteira = $investimentoController->obterCarteiraUsuario($userId);
                                        
                                        // Cria um mapa de transações por ativo para facilitar o acesso ao ID
                                        $mapaTransacoes = [];
                                        foreach ($historico as $transacao) {
                                            if (!isset($mapaTransacoes[$transacao['ativo_id']])) {
                                                $mapaTransacoes[$transacao['ativo_id']] = [];
                                            }
                                            $mapaTransacoes[$transacao['ativo_id']][] = $transacao;
                                        }
                                        
                                        if (empty($carteira)): 
                                        ?>
                                            <tr>
                                                <td colspan="3" class="empty-table-message">
                                                    Nenhum ativo na carteira. Adicione um investimento!
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($carteira as $ativo): ?>
                                                <?php 
                                                // Obtém o ID da primeira transação de compra deste ativo
                                                $transacaoIds = [];
                                                if (isset($mapaTransacoes[$ativo['ativo_id']])) {
                                                    $transacaoIds = array_map(fn($t) => $t['transacao_id'], $mapaTransacoes[$ativo['ativo_id']]);
                                                }
                                                ?>
                                                <tr data-ativo-id="<?= $ativo['ativo_id'] ?>">
                                                    <td class="transaction-name">
                                                        <?= htmlspecialchars($ativo['asset_name']) ?> 
                                                        <small>(<?= htmlspecialchars($ativo['asset_symbol']) ?>)</small>
                                                    </td>
                                                    <td class="amount-income align-right">
                                                        <?= $ativo['total_cotas'] ?>
                                                    </td>
                                                    <td class="align-center">
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <?php foreach ($transacaoIds as $transacaoId): ?>
                                                                <button type="button" class="btn btn-danger btn-apagar" data-transacao-id="<?= $transacaoId ?>" title="Apagar esta transação">
                                                                    <i class='bx bx-trash'></i>
                                                                </button>
                                                            <?php endforeach; ?>
                                                        </div>
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



    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../template/asset/js/Investimento.js"></script>
</body>

</html>