<?php
/*
|--------------------------------------------------------------------------
| A View (ExtratoView.php)
|--------------------------------------------------------------------------
| Este é o "Template" HTML.
| Ele é chamado pelo ExtratoController.
| Todas as variáveis ($userName, $groupedTransactions) vêm do Controller.
*/

/*
|--------------------------------------------------------------------------
| Funções Auxiliares de Formatação (View Helpers)
|--------------------------------------------------------------------------
*/

// Função auxiliar para formatar valores
if (!function_exists('formatCurrency')) {
    function formatCurrency($value)
    {
        $formatted = number_format(abs($value), 2, ',', '.');
        return ($value >= 0 ? '+ R$ ' : '- R$ ') . $formatted;
    }
}

// Função auxiliar para classes CSS
if (!function_exists('getTransactionTypeClass')) {
    function getTransactionTypeClass($tipo)
    {
        if ($tipo === 'meta') return 'meta';
        if ($tipo === 'renda') return 'renda';
        return 'despesa';
    }
}

// Função auxiliar para ícones
if (!function_exists('getTransactionIcon')) {
    function getTransactionIcon($tipo)
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
                <li><a href="VisaoGeral.php">Visão Geral</a></li>
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
                                        // Variáveis vêm do banco (tipo: 'renda', 'despesa', 'meta')
                                        $typeClass = getTransactionTypeClass($transaction['tipo']);
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