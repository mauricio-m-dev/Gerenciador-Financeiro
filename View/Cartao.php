<?php
// =========================================================
// 1. BLOCÃO PHP: CONEXÃO E BUSCA DE DADOS (MOVIDO PARA O TOPO)
// =========================================================

// Certifique-se de que a conexão está incluída e funcionando
require_once '../Config/conexao.php'; // Ajuste o caminho conforme necessário
require_once'../Controller/UserController.php';
$cartoes = []; // Array para armazenar os cartões

// Prepara a consulta para buscar ID, nome, últimos 4 dígitos, bandeira e tipo
$sql = "SELECT id, nome, ultimos4, bandeira, tipo FROM cartoes ORDER BY id ASC";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    // Loop para preencher o array $cartoes
    while ($cartao = $result->fetch_assoc()) {
        $cartoes[] = $cartao;
    }

    $stmt->close();
} else {
    // Em produção, você pode remover ou comentar isso:
    // echo "Erro ao preparar a consulta: " . $conn->error;
}
// Não feche a conexão aqui se for usá-la em outros lugares!


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador - testes</title>
    <link rel="stylesheet" href="../template/asset/css/Cartao.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


</head>

<body>
    <header class="header">
        <div class="brand">NONYX</div>

        <nav class="nav-links">
            <a href="#" data-index="0" class="active">Visão Geral</a>
            <a href="#" data-index="1">Investimentos</a>
            <a href="#" data-index="2">Análise</a>
            <a href="#" data-index="3">Metas</a>
            <a href="#" data-index="4">Cartões</a>
        </nav>

        <div class="profile-settings">
            <i class="fas fa-cog settings-icon"></i>
            <img src="" alt="Foto de Perfil" class="profile-pic" />
        </div>
    </header>
    <main class="main-content1">

        <h1>Cartões</h1>
        <div class="add-cartao" data-bs-toggle="modal" data-bs-target="#modalAddCartao" style="cursor: pointer;">
            <i class='bx bx-plus'></i>
            <span>Adicionar Cartão</span>
        </div>

        </div>

        <div class="Minha-carteira">
            <h2> Minhas Carteiras</h2>
            <h5>Selecione uma carteira para ver os detalhes</h5>

            <div class="delete-cartao-container">
                <button id="btn-delete-card-trigger" class="btn btn-danger btn-sm mb-3" data-bs-toggle="modal"
                    data-bs-target="#modalDeleteCartao">
                    <i class='bx bx-trash'></i> Deletar Cartão
                </button>
            </div>
            <div class="card-list">
                <?php
                // Mapa de cores para as bandeiras
                $cores_cartao = [
                    'Visa' => '#0054a6',
                    'Mastercard' => '#f7971b',
                    'Elo' => '#00a89d',
                    'American Express' => '#4c93d9',
                    'default' => '#3498db', // Cor padrão
                ];

                // Verifica se há cartões antes de iniciar o loop
                if (!empty($cartoes)) {
                    foreach ($cartoes as $cartao) {
                        // Define a cor com base na bandeira, usando a cor default se não encontrar
                        $bandeira_key = array_key_exists($cartao['bandeira'], $cores_cartao)
                            ? $cartao['bandeira']
                            : 'default';
                        $cor = $cores_cartao[$bandeira_key];
                        ?>

                        <div class="card-placeholder card-carteira" style="border-left: 5px solid <?php echo $cor; ?>;"
                            data-card-id="<?php echo $cartao['id']; ?>"
                            data-card-bandeira="<?php echo htmlspecialchars($cartao['bandeira']); ?>">
                            <div class="card-header-info">
                                <p class="card-name">
                                    <?php echo htmlspecialchars($cartao['nome']); ?>
                                </p>
                                <p class="card-number">
                                    **** **** **** <?php echo htmlspecialchars($cartao['ultimos4']); ?>
                                </p>
                            </div>
                            <div class="card-type">
                                <span class="badge badge-<?php echo $cartao['tipo']; ?>">
                                    <?php echo ucfirst($cartao['tipo']); ?>
                                </span>
                            </div>
                        </div>

                        <?php
                    } // Fim do loop foreach
                } else {
                    // Mensagem se não houver cartões cadastrados
                    echo '<p style="margin-top: 15px; color: #777;">Nenhum cartão cadastrado. Use o botão "Adicionar Cartão" para começar.</p>';
                }
                ?>

            </div>
        </div>

        <div class="main-content2">
            <div class="Renda mini-summary-card income-card">
                <h4 class="summary-title">Renda</h4>
                <p class="summary-value" id="renda-valor">R$<?php echo number_format($totalRenda, 2, ',', '.'); ?></p>
                


                </span>
            </div>

            <div class="Despesas mini-summary-card expense-card">
                <h4 class="summary-title">Despesas</h4>
           <p class="summary-value" id="despesas-valor">R$<?php echo number_format($totalDespesas, 2, ',', '.'); ?></p>
               


                </span>
            </div>

            <div class="Metas mini-summary-card goal-card">
                <h4 class="summary-title">Metas</h4>
                <p class="summary-value goal-value" id="metas-valor">R$00,00</p>
                

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
            <?php
            // Percorre o array de despesas por categoria gerado pelo PHP (HTML 1)
            foreach ($expensesByCategory as $expenseItem) {
                $label = htmlspecialchars($expenseItem['label']);
                // Obtém a classe CSS correta (HTML 2)
                $css_class = $category_colors_map[$label] ?? 'dot-default';
                // Obtém o ícone (Funcionalidade do HTML 1)
                $icon = get_icon_for_category($label);

                $percentage = number_format($expenseItem['porcentagem'], 2, ',', '.') . '%';
                ?>
                <li class="category-item">
                    <span class="category-icon-wrapper <?php echo $css_class; ?>">
                        <?php echo $icon; ?>
                    </span>
                    <?php echo $label; ?>
                    <span class="percentage"><?php echo $percentage; ?></span>
                </li>
            <?php } 
            // Função para gerar o ícone (Vinda da sua funcionalidade PHP/HTML 1)
function get_icon_for_category($label) {
    if ($label === 'Casa') { return '<i class=\'bx bx-home-alt\'></i>'; }
    if ($label === 'Cartão de crédito') { return '<i class=\'bx bx-credit-card-alt\'></i>'; }
    if ($label === 'Transporte') { return '<i class=\'bx bx-car\'></i>'; }
    if ($label === 'Alimentação' || $label === 'Mantimentos') { return '<i class=\'bx bx-lemon\'></i>'; }
    if ($label === 'Compras') { return '<i class=\'bx bx-shopping-bag\'></i>'; }
    if ($label === 'Saúde') { return '<i class=\'bx bx-plus-medical\'></i>'; }
    return '<i class=\'bx bx-tag\'></i>'; // Ícone padrão
}?>
        </ul>
    </div>

    <div class="transactions-section">
        <h2>Últimas transações</h2>
        <p class="section-subtitle">Verifique suas últimas transações</p>

        <div class="transactions-list">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>Método</th>
                        <th>Data</th>
                        <th>Quantia</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Usa as transações buscadas do Model (HTML 1)
                    foreach ($latestTransactions as $transaction) {
                        $isIncome = $transaction['quantia'] > 0;
                        $class = $isIncome ? 'income' : 'expense';
                        $quantiaFormatada = number_format(abs($transaction['quantia']), 2, ',', '.');
                        $sinal = $isIncome ? '+' : '-';
                        $data_formatada = date('Y/m/d', strtotime($transaction['data']));

                        $initials = strtoupper(substr($transaction['descricao'], 0, 1));
                        // Define a tag inicial APENAS com a primeira letra, usando uma classe de fundo genérica ou a que você definiu como padrão.
                        $initials_tag = "<span class=\"initials default-bg\">{$initials}</span>";
                    ?>
                        <tr>
                            <td><?php echo $initials_tag; ?> <?php echo htmlspecialchars($transaction['descricao']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['metodo']); ?></td>
                            <td><?php echo htmlspecialchars($data_formatada); ?></td>
                            <td class="<?php echo $class; ?>"><?php echo $sinal; ?>R$<?php echo $quantiaFormatada; ?></td>
                           
                        </tr>
                    <?php
                    }
                    if (empty($latestTransactions)) {
                        echo '<tr><td colspan="5" style="text-align: center; color: #777;">Nenhuma transação recente.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    </main>
    <div class="modal fade" id="modalAddCartao" tabindex="-1" aria-labelledby="modalAddCartaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddCartaoLabel">Adicionar Novo Cartão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <form id="formAddCartao" method="POST" action="../config/salvar_cartao.php">

                        <div class="mb-3">
                            <label for="nomeCartao" class="form-label">Nome do Cartão</label>
                            <input type="text" class="form-control" id="nomeCartao" name="nomeCartao"
                                placeholder="Ex: Cartão Pessoal" required>
                        </div>

                        <div class="mb-3">
                            <label for="numeroCartao" class="form-label">Número do Cartão</label>
                            <input type="text" class="form-control" id="numeroCartao" name="numeroCartao" maxlength="19"
                                placeholder="XXXX XXXX XXXX XXXX" required>
                        </div>

                        <div class="mb-3">
                            <label for="validadeCartao" class="form-label">Data de Validade</label>
                            <input type="month" class="form-control" id="validadeCartao" name="validadeCartao" required>
                        </div>



                        <div class="mb-3">
                            <label for="bandeiraCartao" class="form-label">Bandeira</label>
                            <select class="form-select" id="bandeiraCartao" name="bandeiraCartao" required>
                                <option value="">Selecione</option>
                                <option value="Visa">Visa</option>
                                <option value="Mastercard">Mastercard</option>
                                <option value="Elo">Elo</option>
                                <option value="American Express">American Express</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tipoCartao" class="form-label">Tipo de Cartão</label>
                            <select class="form-select" id="tipoCartao" name="tipoCartao" required>
                                <option value="">Selecione</option>
                                <option value="credito">Crédito</option>
                                <option value="debito">Débito</option>
                                <option value="pre-pago">Pré-pago</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="limiteCartao" class="form-label">Limite (R$)</label>
                            <input type="number" step="0.01" class="form-control" id="limiteCartao" name="limiteCartao"
                                placeholder="Ex: 2000.00">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Salvar Cartão</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalDeleteCartao" tabindex="-1" aria-labelledby="modalDeleteCartaoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDeleteCartaoLabel">Selecione o Cartão para Deletar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p>Selecione um cartão.</p>
                    <select class="form-select" id="selectCardToDelete" required>
                        
                        <?php
                        // Populando o SELECT com os cartões buscados no topo da página
                        if (!empty($cartoes)) {
                            foreach ($cartoes as $cartao_opt) {
                                echo '<option value="' . htmlspecialchars($cartao_opt['id']) . '">';
                                echo htmlspecialchars($cartao_opt['nome']) . ' (Final: ' . htmlspecialchars($cartao_opt['ultimos4']) . ')';
                                echo '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteCardBtn">Deletar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="../template/asset/js/Cartao.js"></script>

</body>

</html>