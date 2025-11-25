<?php
// Helpers visuais
function get_icon($label) {
    $l = mb_strtolower(trim($label));
    if (strpos($l, 'casa')!==false) return "<i class='bx bx-home-alt'></i>";
    if (strpos($l, 'crédito')!==false) return "<i class='bx bx-credit-card-alt'></i>";
    if (strpos($l, 'transporte')!==false || strpos($l, 'carro')!==false) return "<i class='bx bx-car'></i>";
    if (strpos($l, 'alimentação')!==false || strpos($l, 'mercado')!==false) return "<i class='bx bx-lemon'></i>";
    if (strpos($l, 'saúde')!==false) return "<i class='bx bx-plus-medical'></i>";
    return "<i class='bx bx-tag'></i>"; 
}
$colors = ['Casa'=>'dot-home','Alimentação'=>'dot-food','Transporte'=>'dot-transport','Lazer'=>'dot-leisure','Saúde'=>'dot-health'];
?>
<?php
// Demo data for visualisation when no real data is provided
if (!isset($cartoes) || empty($cartoes)) {
    $cartoes = [
        ['id'=>1,'nome'=>'Cartão Visa','bandeira'=>'Visa','tipo'=>'credito','ultimos4'=>'1234'],
        ['id'=>2,'nome'=>'Cartão Mastercard','bandeira'=>'Mastercard','tipo'=>'debito','ultimos4'=>'5678'],
        ['id'=>3,'nome'=>'Cartão Elo','bandeira'=>'Elo','tipo'=>'credito','ultimos4'=>'9012']
    ];
    $activeCardId = $cartoes[0]['id'];
}
if (!isset($resumo)) {
    $resumo = ['renda'=>1500.00,'despesa'=>800.00,'meta'=>3000.00];
}
if (!isset($expensesByCategory)) {
    $expensesByCategory = [
        ['label'=>'Alimentação','porcentagem'=>40],
        ['label'=>'Transporte','porcentagem'=>30],
        ['label'=>'Lazer','porcentagem'=>20],
        ['label'=>'Saúde','porcentagem'=>10]
    ];
}
if (!isset($latestTransactions)) {
    $latestTransactions = [
        ['descricao'=>'Supermercado','metodo'=>'Cartão','data'=>date('Y-m-d'),'quantia'=>-150.75,'tipo'=>'despesa'],
        ['descricao'=>'Salário','metodo'=>'Transferência','data'=>date('Y-m-d'),'quantia'=>2500.00,'tipo'=>'renda'],
        ['descricao'=>'Uber','metodo'=>'Cartão','data'=>date('Y-m-d'),'quantia'=>-45.00,'tipo'=>'despesa']
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador - Cartões</title>
    <link rel="stylesheet" href="../template/asset/css/global.css">
    <link rel="stylesheet" href="../template/asset/css/Cartao.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
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
                <div class="hamburger"><span class="line"></span><span class="line"></span><span class="line"></span></div>
                <a href="#" class="logo">NOVYX</a>
            </div>
            <ul class="nav-links">
                <li><a href="VisaoGeral.php">Visão Geral</a></li>
                <li><a href="Investimento.php">Investimentos</a></li>
                <li><a href="Analise.php">Análise</a></li>
                <li><a href="Meta.php">Metas</a></li>
                <li><a href="CartaoView.php" class="active">Cartões</a></li>
            </ul>
            <div class="user-area">
                <a href="#" class="settings-icon"><i class='bx bx-cog'></i></a>
            </div>
        </nav>
    </header>
    
    <main class="main-content1">
        <h1>Cartões</h1>
        
        <div class="add-cartao" data-bs-toggle="modal" data-bs-target="#modalAddCartao" style="cursor: pointer;">
            <i class='bx bx-plus'></i> <span>Adicionar Cartão</span>
        </div>

        <div class="Minha-carteira">
            <h2>Minhas Carteiras</h2>
            <h5>Selecione uma carteira para ver os detalhes</h5>

            <div class="delete-cartao-container">
                <button id="btn-delete-card-trigger" class="btn btn-danger btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#modalDeleteCartao">
                    <i class='bx bx-trash'></i> Deletar Cartão
                </button>
            </div>
            
            <div class="card-list">
                <?php
                $cardColors = ['Visa'=>'#0054a6', 'Mastercard'=>'#f7971b', 'Elo'=>'#00a89d', 'American Express'=>'#4c93d9', 'default'=>'#3498db'];
                if (!empty($cartoes)):
                    foreach ($cartoes as $c):
                        $cor = $cardColors[$c['bandeira']] ?? $cardColors['default'];
                        $isActive = ($c['id'] == $activeCardId);
                        $borderStyle = "border-left: 5px solid $cor;";
                        $extraClass = $isActive ? "active" : "";
                        $extraStyle = $isActive ? "border: 2px solid #6C5CE7; transform: scale(1.02);" : "";
                ?>
                    <div class="card-placeholder card-carteira <?= $extraClass ?>" 
                         style="<?= $borderStyle . $extraStyle ?>"
                         data-card-id="<?= $c['id'] ?>">
                        <div class="card-header-info">
                            <p class="card-name"><?= htmlspecialchars($c['nome']) ?></p>
                            <p class="card-number">**** <?= htmlspecialchars($c['ultimos4']) ?></p>
                        </div>
                        <div class="card-type"><span class="badge badge-<?= $c['tipo'] ?>"><?= ucfirst($c['tipo']) ?></span></div>
                    </div>
                <?php endforeach; else: ?>
                    <p style="color:#777; padding:10px;">Nenhum cartão cadastrado.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="main-content2">
            <div class="Renda mini-summary-card income-card">
                <h4 class="summary-title">Renda</h4>
                <p class="summary-value" id="renda-valor">R$ <?= number_format($resumo['renda'], 2, ',', '.') ?></p>
            </div>
            <div class="Despesas mini-summary-card expense-card">
                <h4 class="summary-title">Despesas</h4>
                <p class="summary-value" id="despesas-valor">R$ <?= number_format($resumo['despesa'], 2, ',', '.') ?></p>
            </div>
            <div class="Metas mini-summary-card goal-card">
                <h4 class="summary-title">Guardado</h4>
                <p class="summary-value goal-value" id="metas-valor">R$ <?= number_format($resumo['meta'], 2, ',', '.') ?></p>
            </div>
        </div>
        
        <div class="main-content3">
            <div class="chart-section">
                <div class="sidebar-area">
                    <div class="category-chart-section">
                        <h2 class="section-title">Despesas por Categoria</h2>
                        <div class="chart-card">
                            <canvas id="expenseDoughnutChart"
                                data-labels='<?= json_encode(array_column($expensesByCategory, "label")) ?>'
                                data-valores='<?= json_encode(array_column($expensesByCategory, "porcentagem")) ?>'>
                            </canvas>
                        </div>
                    </div>
                </div>
                <ul class="category-list">
                    <?php if(!empty($expensesByCategory)): foreach($expensesByCategory as $item): 
                        $pct = number_format($item['porcentagem'], 2, ',', '.');
                        $cls = $colors[$item['label']] ?? 'dot-default';
                    ?>
                        <li class="category-item">
                            <span class="category-icon-wrapper <?= $cls ?>"><?= get_icon($item['label']) ?></span>
                            <?= htmlspecialchars($item['label']) ?>
                            <span class="percentage"><?= $pct ?>%</span>
                        </li>
                    <?php endforeach; else: echo "<li style='color:#999'>Sem dados</li>"; endif; ?>
                </ul>
            </div>

            <div class="transactions-section">
                <h2>Últimas transações</h2>
                <div class="transactions-list">
                    <table class="transactions-table">
                        <thead><tr><th>Descrição</th><th>Método</th><th>Data</th><th>Quantia</th></tr></thead>
                        <tbody>
                            <?php if(!empty($latestTransactions)): foreach($latestTransactions as $t): 
                                $val = floatval($t['quantia']);
                                $isInc = ($t['tipo'] == 'renda' || $val > 0);
                                $cls = $isInc ? 'income' : 'expense';
                                $sig = $isInc ? '+' : '';
                                $date = date('d/m/Y', strtotime($t['data']));
                                $init = strtoupper(substr($t['descricao'],0,1));
                            ?>
                                <tr>
                                    <td><span class="initials default-bg"><?= $init ?></span> <?= htmlspecialchars($t['descricao']) ?></td>
                                    <td><?= htmlspecialchars($t['metodo']??'Geral') ?></td>
                                    <td><?= $date ?></td>
                                    <td class="<?= $cls ?>"><?= $sig ?>R$ <?= number_format(abs($val), 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="4" style="text-align:center; color:#777">Nenhuma transação recente.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    
    <!-- MODAIS -->
    <!-- Modal Adicionar -->
    <div class="modal fade" id="modalAddCartao" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Novo Cartão</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form id="formAddCartao" method="POST" action="../config/salvar_cartao.php"><div class="mb-3"><label>Nome</label><input type="text" class="form-control" name="nomeCartao" required></div><div class="mb-3"><label>Número</label><input type="text" class="form-control" id="numeroCartao" name="numeroCartao" maxlength="19" required></div><div class="mb-3"><label>Validade</label><input type="month" class="form-control" name="validadeCartao" required></div><div class="mb-3"><label>Bandeira</label><select class="form-select" name="bandeiraCartao" required><option value="Visa">Visa</option><option value="Mastercard">Mastercard</option><option value="Elo">Elo</option></select></div><div class="mb-3"><label>Tipo</label><select class="form-select" id="tipoCartao" name="tipoCartao" required><option value="credito">Crédito</option><option value="debito">Débito</option></select></div><div class="mb-3" id="divLimite" style="display:none"><label>Limite</label><input type="number" step="0.01" class="form-control" name="limiteCartao"></div><button type="submit" class="btn btn-primary w-100">Salvar</button></form></div></div></div></div>
    <!-- Modal Deletar -->
    <div class="modal fade" id="modalDeleteCartao" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Excluir Cartão</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><select class="form-select" id="selectCardToDelete"><option value="">Selecione...</option><?php foreach($cartoes as $c){ echo "<option value='{$c['id']}'>{$c['nome']}</option>"; } ?></select></div><div class="modal-footer"><button type="button" class="btn btn-danger" id="confirmDeleteCardBtn">Deletar</button></div></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    <script src="../template/asset/js/Cartao.js"></script>
</body>
</html>