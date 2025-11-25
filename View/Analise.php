<?php
/*
|--------------------------------------------------------------------------
| View: Analise.php (Limpo e Integrado)
|--------------------------------------------------------------------------
*/
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: Login.php');
    exit;
}

$raiz = dirname(__DIR__);
require_once $raiz . '/Config/configuration.php';
require_once $raiz . '/Model/Connection.php';
require_once $raiz . '/Model/AnaliseModel.php';
require_once $raiz . '/Controller/AnaliseController.php';

use Model\Connection;
use Controller\AnaliseController;

try {
    $pdo = Connection::getInstance();
    $controller = new AnaliseController($pdo);
    $userId = $_SESSION['id'];

    $mes = $_GET['mes'] ?? date('m');
    $ano = $_GET['ano'] ?? date('Y');

    // Busca TODOS os dados processados pelo Controller
    $d = $controller->index($userId, $mes, $ano);

    // --- Helpers de Visualização (Setas e Formatação) ---
    function formatCurrency($value)
    {
        return 'R$ ' . number_format((float) $value, 2, ',', '.');
    }

    function getArrow($pct, $invert = false)
    {
        $isPositive = $pct >= 0;
        $color = $isPositive ? 'green' : 'red';
        if ($invert)
            $color = $isPositive ? 'red' : 'green';

        $svgPath = $isPositive
            ? 'M14 2.5a.5.5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0z'
            : 'M14 13.5a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1 0-1h4.793L2.146 2.854a.5.5 0 1 1 .708-.708L13 12.293V7.5a.5.5 0 0 1 1 0z';

        return [
            'class' => $color,
            'path' => $svgPath,
            'text' => ($isPositive ? '+' : '') . number_format($pct, 1, ',', '.') . '%'
        ];
    }

    // Configura as setas usando os dados do Controller ($d)
    $setaRenda = getArrow($d['pctRenda']);
    $setaDespesa = getArrow($d['pctDespesa'], true); // Invertido
    $setaMetas = getArrow($d['pctMetas']);
    $setaInvestimentos = getArrow($d['pctInvestimentos']);

    // Nome do mês para exibição
    $meses = [1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'];
    $nomeMesAtual = $meses[(int) $mes];

} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}

// Dicas (Mantidas igual)
$bancoDeDicas = [
    ['titulo' => 'Regra 50-30-20', 'texto' => 'Divida sua renda: 50% necessidades, 30% desejos, 20% investimentos.', 'cor' => 'green-bg'],
    ['titulo' => 'Reserva de Emergência', 'texto' => 'Guarde de 6 a 12 meses do seu custo de vida.', 'cor' => 'yellow-bg'],
    ['titulo' => 'Evite Dívidas', 'texto' => 'Pague sempre o total da fatura do cartão.', 'cor' => 'green-bg'],
    ['titulo' => 'Metas Claras', 'texto' => 'Defina objetivos de curto, médio e longo prazo.', 'cor' => 'green-bg'],
    ['titulo' => 'Pague-se Primeiro', 'texto' => 'Invista assim que receber o salário.', 'cor' => 'green-bg'],
    ['titulo' => 'Revise Gastos', 'texto' => 'Cancele assinaturas que não usa.', 'cor' => 'yellow-bg']
];
shuffle($bancoDeDicas);
$dicasExibidas = array_slice($bancoDeDicas, 0, 4);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOVYX - Análise</title>
    <link rel="stylesheet" href="../template/asset/css/Analise.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                <div class="hamburger"><span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
                <a href="#" class="logo">NOVYX</a>
            </div>
            <ul class="nav-links">
                <li><a href="VisaoGeral.php">Visão Geral</a></li>
                <li><a href="Investimento.php">Investimentos</a></li>
                <li><a href="Analise.php" class="active">Análise</a></li>
                <li><a href="Meta.php">Metas</a></li>
                <li><a href="CartaoView.php">Cartões</a></li>
            </ul>
            <div class="user-area">
                <a href="#" class="settings-icon"><i class='bx bx-cog'></i></a>
            </div>
        </nav>
    </header>

    <main style="padding: 100px 2rem; background-color: rgba(249, 249, 249, 1);">
        <div class="container">

            <div class="title"
                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1 style="margin: 0;">Análise Financeira</h1>
                <form method="GET" style="display: flex; gap: 10px;">
                    <select name="mes" onchange="this.form.submit()" style="padding: 5px; border-radius: 8px;">
                        <?php foreach ($meses as $k => $v)
                            echo "<option value='$k' " . ($k == $mes ? 'selected' : '') . ">$v</option>"; ?>
                    </select>
                    <select name="ano" onchange="this.form.submit()" style="padding: 5px; border-radius: 8px;">
                        <?php for ($i = date('Y'); $i >= 2023; $i--)
                            echo "<option value='$i' " . ($i == $ano ? 'selected' : '') . ">$i</option>"; ?>
                    </select>
                </form>
            </div>

            <div class="geral-container">
                <div class="geral-card-renda">
                    <p class="mini">Renda</p>
                    <h2 class="h2-renda"><?= formatCurrency($d['renda']) ?></h2>
                    <p class="<?= $setaRenda['class'] ?>">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="<?= $setaRenda['path'] ?>" />
                        </svg>
                        <?= $setaRenda['text'] ?> vs mês anterior
                    </p>
                </div>
                <div class="geral-card-despesas">
                    <p class="mini">Despesas</p>
                    <h2 class="h2-despesas"><?= formatCurrency($d['despesa']) ?> </h2>
                    <p class="<?= $setaDespesa['class'] ?>">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="<?= $setaDespesa['path'] ?>" />
                        </svg>
                        <?= $setaDespesa['text'] ?> vs mês anterior
                    </p>
                </div>
                <div class="geral-card-metas">
                    <p class="mini">Metas (Aportes)</p>
                    <h2 class="h2-metas"><?= formatCurrency($d['metas']) ?></h2>
                    <p class="<?= $setaMetas['class'] ?>">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="<?= $setaMetas['path'] ?>" />
                        </svg>
                        <?= $setaMetas['text'] ?> vs mês anterior
                    </p>
                </div>
            </div>

            <div class="geral-container">
                <div class="chart-card">
                    <h3>Evolução Patrimonial</h3>
                    <p class="sub-title">Últimos 6 meses</p>
                    <div class="chart-content">
                        <canvas id="evolution-chart" data-labels='<?= json_encode($d['evoLabels']) ?>'
                            data-valores='<?= json_encode($d['evoValores']) ?>'></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <h3>Despesas por Categoria</h3>
                    <p class="sub-title">Distribuição Mensal</p>
                    <div class="chart-content">
                        <canvas id="expenses-chart" data-labels='<?= json_encode($d['catLabels']) ?>'
                            data-valores='<?= json_encode($d['catValores']) ?>'></canvas>
                    </div>
                </div>
            </div>

            <div class="comparativo">
                <h3 class="mensal">Comparativo Mensal</h3>
                <p class="mes"><?= $nomeMesAtual ?> <?= $ano ?> vs Mês Anterior</p>

                <div class="comparison-card">
                    <div class="comparison-details">
                        <h3>Renda</h3>
                        <p class="prev-value">Anterior: <?= formatCurrency($d['rendaAnt']) ?></p>
                    </div>
                    <div class="comparison-metrics">
                        <span class="current-value"><?= formatCurrency($d['renda']) ?></span>
                        <span class="growth <?= $setaRenda['class'] ?>"><?= $setaRenda['text'] ?></span>
                    </div>
                </div>

                <div class="comparison-card">
                    <div class="comparison-details">
                        <h3>Despesas</h3>
                        <p class="prev-value">Anterior: <?= formatCurrency($d['despesaAnt']) ?></p>
                    </div>
                    <div class="comparison-metrics">
                        <span class="current-value"><?= formatCurrency($d['despesa']) ?></span>
                        <span class="growth <?= $setaDespesa['class'] ?>"><?= $setaDespesa['text'] ?></span>
                    </div>
                </div>

                <div class="comparison-card">
                    <div class="comparison-details">
                        <h3>Metas</h3>
                        <p class="prev-value">Anterior: <?= formatCurrency($d['metasAnt']) ?></p>
                    </div>
                    <div class="comparison-metrics">
                        <span class="current-value"><?= formatCurrency($d['metas']) ?></span>
                        <span class="growth <?= $setaMetas['class'] ?>"><?= $setaMetas['text'] ?></span>
                    </div>
                </div>

                <div class="comparison-card">
                    <div class="comparison-details">
                        <h3>Investimentos</h3>
                        <p class="prev-value">Anterior: <?= formatCurrency($d['investimentosAnt']) ?></p>
                    </div>
                    <div class="comparison-metrics">
                        <span class="current-value"><?= formatCurrency($d['investimentos']) ?></span>
                        <span class="growth <?= $setaInvestimentos['class'] ?>"><?= $setaInvestimentos['text'] ?></span>
                    </div>
                </div>
            </div>

            <div class="recomendacoes-container">
                <div class="title-rec">
                    <h3 class="recomen">Recomendações</h3>
                    <p class="rec">Dicas para organizar suas finanças hoje</p>
                </div>
                <div class="recomendacoes-content">
                    <?php foreach ($dicasExibidas as $dica): ?>
                        <div class="reco-card <?= $dica['cor'] ?>">
                            <h3><?= $dica['titulo'] ?></h3>
                            <p><?= $dica['texto'] ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../template/asset/js/Analise.js?v=3"></script>
</body>

</html>