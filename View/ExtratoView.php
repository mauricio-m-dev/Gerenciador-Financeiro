<?php
if (!function_exists('formatCurrency')) {
    function formatCurrency($value) {
        $formatted = number_format(abs($value), 2, ',', '.');
        return ($value >= 0 ? '+ R$ ' : '- R$ ') . $formatted;
    }
}
if (!function_exists('getTransactionTypeClass')) {
    function getTransactionTypeClass($tipo) { return ($tipo === 'renda') ? 'renda' : 'despesa'; }
}
if (!function_exists('getTransactionIcon')) {
    function getTransactionIcon($tipo) {
        if ($tipo === 'renda') return 'bx-trending-up';
        if ($tipo === 'despesa') return 'bx-trending-down';
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../template/asset/css/Extrato.css">
    <style>
        /* Estilo para o botão de deletar no card do extrato */
        .transaction-actions form { display: flex; align-items: center; }
        .btn-delete-icon { background: none; border: none; cursor: pointer; font-size: 1.2rem; color: #a0a0a0; padding: 5px; border-radius: 50%; transition: 0.2s; }
        .btn-delete-icon:hover { color: #e63946; background-color: rgba(230, 57, 70, 0.1); }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-left">
                <a href="VisaoGeral.php" class="logo">NOVYX</a>
            </div>
            <div class="user-area">
                <img src="<?= htmlspecialchars($userProfilePic) ?>" alt="Perfil" class="profile-pic">
            </div>
        </nav>
    </header>
    <main>
        <div class="container">
            <h1>Extrato Completo</h1>
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
                                    $typeClass = getTransactionTypeClass($transaction['tipo']);
                                    $iconClass = getTransactionIcon($transaction['tipo']);
                                    ?>
                                    <li class="transaction-item card">
                                        <div class="transaction-icon <?= $typeClass ?>"><i class='bx <?= $iconClass ?>'></i></div>
                                        <div class="transaction-details">
                                            <span class="transaction-name"><?= htmlspecialchars($transaction['nome']) ?></span>
                                            <span class="transaction-method"><?= htmlspecialchars($transaction['metodo']) ?></span>
                                        </div>
                                        <div class="transaction-amount <?= $typeClass ?>">
                                            <?= htmlspecialchars(formatCurrency($transaction['quantia'])) ?>
                                        </div>
                                        <div class="transaction-actions">
                                            <form action="../Controller/ExcluirTransacao.php" method="POST" onsubmit="return confirm('Excluir esta transação?');">
                                                <input type="hidden" name="id_transacao" value="<?= $transaction['id'] ?>">
                                                <input type="hidden" name="origin" value="extrato">
                                                <button type="submit" class="btn-delete-icon"><i class='bx bx-trash'></i></button>
                                            </form>
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
</body>
</html>