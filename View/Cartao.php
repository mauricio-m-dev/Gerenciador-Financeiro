<?php 
// =========================================================
// 1. BLOCÃO PHP: CONEXÃO E BUSCA DE DADOS (MOVIDO PARA O TOPO)
// =========================================================

// Certifique-se de que a conexão está incluída e funcionando
require_once '../Config/conexao.php'; // Ajuste o caminho conforme necessário

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

// Dados de exemplo para o gráfico (mantido)
$labels = ['Casa', 'Cartão de crédito', 'Transporte', 'Mantimentos', 'Compras'];
$valores = [4135, 2151, 1347, 997, 335]; 

$chartLabelsJSON = json_encode($labels);
$chartValoresJSON = json_encode($valores);

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    

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
        <button id="btn-delete-card-trigger" class="btn btn-danger btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#modalDeleteCartao">
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
                    
                    <div 
                        class="card-placeholder card-carteira" 
                        style="border-left: 5px solid <?php echo $cor; ?>;"
                        data-card-id="<?php echo $cartao['id']; ?>"
                        data-card-bandeira="<?php echo htmlspecialchars($cartao['bandeira']); ?>"
                    >
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
                <p class="summary-value" id="renda-valor">R$5.502,45</p> 
                <div class="inner-block small2 increase-indicator">
    <i class='bx bx-up-arrow-alt'></i> 12,5%
</div>


                </span>
            </div>

            <div class="Despesas mini-summary-card expense-card">
                <h4 class="summary-title">Despesas</h4>
                <p class="summary-value" id="despesas-valor">R$9.450,00</p> 
                <div class="inner-block small2 increase-indicator">
    <i class='bx bx-up-arrow-alt'></i> 27,1%
</div>


                </span>
            </div>

            <div class="Metas mini-summary-card goal-card">
                <h4 class="summary-title">Metas</h4>
                <p class="summary-value goal-value" id="metas-valor">R$3.945,55</p> 
                <div class="inner-block small2 decrease-indicator">
    <i class='bx bx-down-arrow-alt'></i> -15% 
</div>

            </div>
        </div>
        <div class="main-content3">
    
    <div class="chart-section">
    <div class="sidebar-area">
                    <div class="category-chart-section">
                        <h2 class="section-title">Despesas por Categoria</h2>
                        <div class="chart-card">
                            <canvas id="expenseDoughnutChart" data-labels='<?= $chartLabelsJSON ?>'
                                data-valores='<?= $chartValoresJSON ?>'></canvas>
                        </div>
                    </div>
                </div>
        

    <ul class="category-list">
        <li class="category-item">
            <span class="category-icon-wrapper dot-casa">
                <i class='bx bx-home-alt'></i> 
            </span> 
            Casa 
            <span class="percentage">41,35%</span>
        </li>
        
        <li class="category-item">
            <span class="category-icon-wrapper dot-cartao">
                <i class='bx bx-credit-card-alt'></i> 
            </span> 
            Cartão de crédito 
            <span class="percentage">21,51%</span>
        </li>
        
        <li class="category-item">
            <span class="category-icon-wrapper dot-transporte">
                <i class='bx bx-car'></i> 
            </span> 
            Transporte 
            <span class="percentage">13,47%</span>
        </li>
        
        <li class="category-item">
            <span class="category-icon-wrapper dot-mantimentos">
                <i class='bx bx-lemon'></i> 
            </span> 
            Mantimentos 
            <span class="percentage">9,97%</span>
        </li>
        
        <li class="category-item">
            <span class="category-icon-wrapper dot-compras">
                <i class='bx bx-shopping-bag'></i> 
            </span> 
            Compras 
            <span class="percentage">3,35%</span>
        </li>
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
                    <th></th> </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="initials orlando">OS</span> Orlando Silva</td>
                    <td>Conta Bancária</td>
                    <td>2024/04/01</td>
                    <td class="income">+R$750,00</td>
                    <td><i class='bx bx-dots-vertical-rounded'></i></td>
                </tr>
                <tr>
                    <td><span class="initials netflix">N</span> Netflix</td>
                    <td>Cartão de crédito</td>
                    <td>2024/03/29</td>
                    <td class="expense">-R$9,90</td>
                    <td><i class='bx bx-dots-vertical-rounded'></i></td>
                </tr>
                <tr>
                    <td><span class="initials spotify"><i class='bx bxl-spotify'></i></span> Spotify</td>
                    <td>Cartão de crédito</td>
                    <td>2024/03/28</td>
                    <td class="expense">-R$19,90</td>
                    <td><i class='bx bx-dots-vertical-rounded'></i></td>
                </tr>
                <tr>
                    <td><span class="initials mercado"><i class='bx bx-store'></i></span> Mercado</td>
                    <td>Conta Bancária</td>
                    <td>2024/03/27</td>
                    <td class="expense">-R$123,45</td>
                    <td><i class='bx bx-dots-vertical-rounded'></i></td>
                </tr>
                <tr>
                    <td><span class="initials uber"><i class='bx bxs-car'></i></span> Uber</td>
                    <td>Cartão de crédito</td>
                    <td>2024/03/26</td>
                    <td class="expense">-R$34,20</td>
                    <td><i class='bx bx-dots-vertical-rounded'></i></td>
                </tr>
                <tr>
                    <td><span class="initials salario"><i class='bx bx-wallet'></i></span> Salário</td>
                    <td>Depósito</td>
                    <td>2024/03/25</td>
                    <td class="income">+R$4.200,00</td>
                    <td><i class='bx bx-dots-vertical-rounded'></i></td>
                </tr>
                <tr>
                    <td><span class="initials loja"><i class='bx bx-shopping-bag'></i></span> Loja Online</td>
                    <td>Cartão de crédito</td>
                    <td>2024/03/24</td>
                    <td class="expense">-R$249,00</td>
                    <td><i class='bx bx-dots-vertical-rounded'></i></td>
                </tr>
                <tr>
                    <td><span class="initials ifood"><i class='bx bx-food-menu'></i></span> iFood</td>
                    <td>Cartão de débito</td>
                    <td>2024/03/23</td>
                    <td class="expense">-R$29,50</td>
                    <td><i class='bx bx-dots-vertical-rounded'></i></td>
                </tr>
                <tr>
                    <td><span class="initials ifood"><i class='bx bx-food-menu'></i></span> iFood</td>
                    <td>Cartão de débito</td>
                    <td>2024/03/23</td>
                    <td class="expense">-R$29,50</td>
                    <td><i class='bx bx-dots-vertical-rounded'></i></td>
                </tr>
                <tr>
                    <td><span class="initials ifood"><i class='bx bx-food-menu'></i></span> iFood</td>
                    <td>Cartão de débito</td>
                    <td>2024/03/23</td>
                    <td class="expense">-R$29,50</td>
                    <td><i class='bx bx-dots-vertical-rounded'></i></td>
                </tr>
                <tr>
                    <td><span class="initials ifood"><i class='bx bx-food-menu'></i></span> iFood</td>
                    <td>Cartão de débito</td>
                    <td>2024/03/23</td>
                    <td class="expense">-R$29,50</td>
                    <td><i class='bx bx-dots-vertical-rounded'></i></td>
                </tr>
                <tr>
                    <td><span class="initials netflix">N</span> Netflix</td>
                    <td>Cartão de crédito</td>
                    <td>2024/03/29</td>
                    <td class="expense">-R$9,90</td>
                    <td><i class='bx bx-dots-vertical-rounded'></i></td>
                </tr>
                </tbody>
    </table>
    </div>

        
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
                        <input type="text" class="form-control" id="nomeCartao" name="nomeCartao" placeholder="Ex: Cartão Pessoal" required>
                    </div>

                    <div class="mb-3">
                        <label for="numeroCartao" class="form-label">Número do Cartão</label>
                        <input type="text" class="form-control" id="numeroCartao" name="numeroCartao" maxlength="19" placeholder="XXXX XXXX XXXX XXXX" required>
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
                        <input type="number" step="0.01" class="form-control" id="limiteCartao" name="limiteCartao" placeholder="Ex: 2000.00">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Salvar Cartão</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalDeleteCartao" tabindex="-1" aria-labelledby="modalDeleteCartaoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteCartaoLabel">Selecione o Cartão para Deletar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Selecione um cartão. **Todas as transações associadas serão apagadas.**</p>
                <select class="form-select" id="selectCardToDelete" required>
                    <option value="">-- Selecione o Cartão --</option>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script src="../template/asset/js/Cartao.js"></script>

</body>

</html>