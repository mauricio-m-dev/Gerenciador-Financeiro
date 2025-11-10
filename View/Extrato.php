<?php
/*
|--------------------------------------------------------------------------
| SIMULAÇÃO DE DADOS DO BACKEND (PÁGINA EXTRATO)
|--------------------------------------------------------------------------
*/

// 1. Dados do Usuário (Sessão)
$userName = "Mauricio";
$userProfilePic = "https://via.placeholder.com/40";

// 2. Simulação de uma lista de transações muito maior
$allTransactions = [
    [
        "nome" => "Spotify", "metodo" => "Cartão de Débito", "data" => "21 Out 2025",
        "quantia" => -29.90, "tipo" => "despesa"
    ],
    [
        "nome" => "Freelance Projeto Y", "metodo" => "Pix", "data" => "21 Out 2025",
        "quantia" => 2300.00, "tipo" => "renda"
    ],
    [
        "nome" => "Aluguel", "metodo" => "Boleto", "data" => "20 Out 2025",
        "quantia" => -1850.00, "tipo" => "despesa"
    ],
    [
        "nome" => "Supermercado Semar", "metodo" => "Cartão de Crédito", "data" => "20 Out 2025",
        "quantia" => -430.50, "tipo" => "despesa"
    ],
    [
        "nome" => "Resgate Poupança", "metodo" => "Transferência", "data" => "20 Out 2025",
        "quantia" => 1000.00, "tipo" => "renda"
    ],
    [
        "nome" => "Cinema", "metodo" => "Cartão de Débito", "data" => "19 Out 2025",
        "quantia" => -75.00, "tipo" => "despesa"
    ],
    [
        "nome" => "iFood", "metodo" => "Cartão de Crédito", "data" => "19 Out 2025",
        "quantia" => -55.80, "tipo" => "despesa"
    ],
    [
        "nome" => "Depósito Meta ", "Viagem", "metodo" => "Transferência", "data" => "18 Out 2025",
        "quantia" => -500.00, "tipo" => "meta" // Adicionando tipo meta
    ],
    [
        "nome" => "Salário Empresa X", "metodo" => "Pix", "data" => "18 Out 2025",
        "quantia" => 5500.00, "tipo" => "renda"
    ],
    [
        "nome" => "Padaria", "metodo" => "Dinheiro", "data" => "17 Out 2025",
        "quantia" => -22.30, "tipo" => "despesa"
    ],
];

/**
 * Função auxiliar para agrupar transações por data.
 * Em um cenário real, isso seria feito com "GROUP BY" no SQL.
 */
function groupTransactionsByDate(array $transactions): array
{
    $grouped = [];
    foreach ($transactions as $t) {
        $date = $t['data']; // A data já está formatada
        if (!isset($grouped[$date])) {
            $grouped[$date] = [];
        }
        $grouped[$date][] = $t;
    }
    return $grouped;
}

$groupedTransactions = groupTransactionsByDate($allTransactions);

// Função auxiliar para formatar valores
function formatCurrency(float $value): string
{
    $formatted = number_format($value, 2, ',', '.');
    return ($value > 0 ? '+ R$ ' : 'R$ ') . $formatted;
}

// Função auxiliar para classes CSS
function getTransactionTypeClass(float $value, string $tipo): string
{
    if ($tipo === 'meta') return 'meta';
    return $value > 0 ? 'renda' : 'despesa';
}

// Função auxiliar para ícones
function getTransactionIcon(string $tipo): string
{
    switch ($tipo) {
        case 'renda':
            return 'bx-trending-up';
        case 'despesa':
            return 'bx-trending-down';
        case 'meta':
            return 'bxs-flag-checkered';
        default:
            return 'bx-dollar';
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extrato - NOVYX</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <link rel="stylesheet" href="../template/asset/css/Extrato.css">
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
                <li><a href="Visaogeral.php">Visão Geral</a></li>
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
            <h1>Transações</h1>

            <div class="extrato-container">
                <?php if (empty($groupedTransactions)): ?>
                    <p class="empty-state">Nenhuma transação encontrada.</p>
                <?php else: ?>
                    
                    <?php foreach ($groupedTransactions as $date => $transactionsOnDate): ?>
                        <div class="transaction-group">
                            <h2 class="transaction-group-date"><?= htmlspecialchars($date) ?></h2>
                            
                            <ul class="transaction-list">
                                <?php foreach ($transactionsOnDate as $transaction): ?>
                                    <?php
                                        $typeClass = getTransactionTypeClass($transaction['quantia'], $transaction['tipo']);
                                        $iconClass = getTransactionIcon($transaction['tipo']);
                                    ?>
                                    <li class="transaction-item card">
                                        <div class="transaction-icon <?= $typeClass ?>">
                                            <i class='bx <?= $iconClass ?>'></i>
                                        </div>
                                        <div class="transaction-details">
                                            <span class="transaction-name"><?= htmlspecialchars($transaction['nome']) ?></span>
                                            <span class="transaction-method"><?= htmlspecialchars($transaction['metodo']) ?></span>
                                        </div>
                                        <div class="transaction-amount <?= $typeClass ?>">
                                            <?= htmlspecialchars(formatCurrency($transaction['quantia'])) ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>

                <?php endif; ?>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="../template/asset/js/Extrato.js"></script>

</body>

</html>