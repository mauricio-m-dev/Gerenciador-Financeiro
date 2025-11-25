<?php
session_start();
require_once '../Config/configuration.php'; // Certifique-se que o arquivo de conexão está aqui
require_once '../Controller/MetaController.php';

// Ajuste para pegar o ID da sessão real do seu sistema
$userId = $_SESSION['id'] ?? 7; 

$pdo = getConexao();
$controller = new MetaController($pdo);
$metas = $controller->index($userId);

// --- CÁLCULO DOS TOTAIS PARA OS CARDS DO TOPO ---
$total_objetivo = 0;
$total_acumulado = 0;
$total_mensal = 0;

foreach ($metas as $m) {
    $total_objetivo += $m['objetivo'];
    $total_acumulado += $m['acumulado'];
    $total_mensal += $m['mensal'];
}

$progresso_geral = ($total_objetivo > 0) ? ($total_acumulado / $total_objetivo) * 100 : 0;

// Formatações para exibir no HTML
$total_objetivo_formatado = $controller->formatar_moeda($total_objetivo);
$total_acumulado_formatado = $controller->formatar_moeda($total_acumulado);
$total_mensal_formatado = $controller->formatar_moeda($total_mensal);
$progresso_geral_formatado = round($progresso_geral) . '%';
$min_date = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gerenciador - Metas</title>
    <link rel="stylesheet" href="../template/asset/css/global.css">
    <link rel="stylesheet" href="../template/asset/css/Meta.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
                <li><a href="Meta.php" class="active">Metas</a></li>
                <li><a href="CartaoView.php">Cartões</a></li>
            </ul>
            <div class="user-area">
                <a href="#" class="settings-icon"><i class='bx bx-cog'></i></a>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="header-meta-section">
                <h1>Metas Financeiras</h1>
                <button class="add-meta-button"><i class='bx bxs-plus'></i>Adicionar Meta</button>
            </div>

            <div class="meta-container">
                <div class="meta-card">
                    <p>Total das Metas</p>
                    <h2><?php echo $total_objetivo_formatado; ?></h2>
                </div>
                <div class="meta-card valor">
                    <p>Valor Acumulado</p>
                    <h2><?php echo $total_acumulado_formatado; ?></h2>
                </div>
                <div class="meta-card progresso">
                    <p>Progresso Geral</p>
                    <h2><?php echo $progresso_geral_formatado; ?></h2>
                </div>
                <div class="meta-card contribuicao">
                    <p>Contribuição Mensal</p>
                    <h2><?php echo $total_mensal_formatado; ?></h2>
                </div>
            </div>

            <div class="meta-details">
                <?php if (empty($metas)): ?>
                    <p style="text-align: center; width: 100%; font-size: 1.2rem; color: #777;">Nenhuma meta encontrada. Adicione uma nova!</p>
                <?php endif; 

                foreach ($metas as $meta):
                    $progresso = $controller->calcular_progresso($meta['acumulado'], $meta['objetivo']);
                    $falta = $meta['objetivo'] - $meta['acumulado'];
                    $prazo_formatado = $controller->formatar_data($meta['prazo']);
                    $meses_restantes = $controller->calcular_diferenca_meses($meta['prazo']);
                    $cor_meta = !empty($meta['cor']) ? $meta['cor'] : '#2a68ff';
                    // Decodifica para evitar erro no htmlspecialchars se estiver nulo
                    $histArray = json_decode($meta['historico_json'] ?? '[]', true);
                    $historico_js = json_encode($histArray);
                ?>

                <div class="card-details" 
                    data-meta-id="<?php echo $meta['id']; ?>"
                    data-meta-goal="<?php echo $meta['objetivo']; ?>"
                    data-meta-history='<?php echo htmlspecialchars($historico_js, ENT_QUOTES, 'UTF-8'); ?>'
                    data-meta-color="<?php echo $cor_meta; ?>">

                    <div class="card-details-header">
                        <div class="goal-info">
                            <h2><?php echo htmlspecialchars($meta['nome']); ?></h2>
                            <p><?php echo htmlspecialchars($meta['categoria']); ?></p>
                        </div>
                        <div class="goal-percentage"><?php echo $progresso; ?>%</div>
                    </div>

                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?php echo $progresso; ?>%; background-color: <?php echo $cor_meta; ?>;"></div>
                    </div>

                    <div class="chart-and-summary">
                        <div class="chart-container">
                            <canvas id="chartMeta-<?php echo $meta['id']; ?>"></canvas>
                        </div>

                        <div class="summary-details-vertical">
                            <div class="summary-item">
                                <i class='bx bx-dollar'></i> <span>Acumulado: <b><?php echo $controller->formatar_moeda($meta['acumulado']); ?></b></span>
                            </div>
                            <div class="summary-item">
                                <i class='bx bx-target-lock'></i> <span>Objetivo: <b><?php echo $controller->formatar_moeda($meta['objetivo']); ?></b></span>
                            </div>

                            <div class="card-actions">
                                <button class="contribute-button" data-meta-id="<?php echo $meta['id']; ?>" data-meta-name="<?php echo $meta['nome']; ?>">
                                    Registrar Aporte
                                </button>
                                <button class="history-button" data-meta-id="<?php echo $meta['id']; ?>" data-meta-name="<?php echo htmlspecialchars($meta['nome']); ?>" data-meta-history='<?php echo htmlspecialchars($historico_js, ENT_QUOTES, 'UTF-8'); ?>'>
                                    <i class='bx bx-history'></i> Histórico
                                </button>
                                <button class="delete-meta-button" data-meta-id="<?php echo $meta['id']; ?>" data-meta-name="<?php echo htmlspecialchars($meta['nome']); ?>">
                                    <i class='bx bx-trash'></i> Excluir
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-details">
                        <div class="timeline-item">
                            <i class='bx bx-calendar'></i> <span>Prazo: <?php echo $prazo_formatado; ?></span>
                        </div>
                        <div class="timeline-item timeline-right">
                            <i class='bx bx-time'></i> <span><?php echo $meses_restantes; ?> meses</span>
                        </div>
                    </div>

                    <?php if($falta > 0): ?>
                    <div class="alert-message">
                        <i class='bx bxs-error-circle'></i>
                        <div>
                            <span>Faltam <b><?php echo $controller->formatar_moeda($falta); ?></b></span>
                            <p>Continue contribuindo para alcançar sua meta!</p>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert-message" style="background-color: #d1fae5; color: #065f46;">
                        <i class='bx bxs-check-circle'></i>
                        <div>
                            <span>Parabéns!</span>
                            <p>Você atingiu esta meta!</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <div id="addMetaModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Adicionar Nova Meta</h2>
                <button id="closeModal" class="close-button">&times;</button>
            </div>
            <form id="newMetaForm">
                <div class="form-group"><label>Nome da Meta:</label><input type="text" name="nome" required></div>
                <div class="form-group"><label>Valor Objetivo (R$):</label><input type="number" name="objetivo" step="0.01" required></div>
                <div class="form-group"><label>Contribuição Mensal (R$):</label><input type="number" name="mensal" step="0.01" required></div>
                <div class="form-group"><label>Prazo Final:</label><input type="date" name="prazo" required></div>
                <div class="form-group"><label>Categoria:</label>
                    <select name="categoria" required>
                        <option value="seguranca">Segurança</option><option value="lazer">Lazer</option><option value="moradia">Moradia</option><option value="veiculo">Veículo</option><option value="educacao">Educação</option>
                    </select>
                </div>
                <div class="form-group"><label>Cor:</label><input type="color" name="cor" value="#2a68ff"></div>
                <button type="submit" class="submit-button">Criar Meta</button>
            </form>
        </div>
    </div>

    <div id="contributeModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header"><h2>Registrar Aporte: <span id="meta-name-display"></span></h2><button id="closeContributeModal" class="close-button">&times;</button></div>
            <form id="contributeForm">
                <input type="hidden" id="meta-id-input" name="meta_id">
                <div class="form-group"><label>Mês:</label><input type="month" name="date" required value="<?php echo date('Y-m'); ?>"></div>
                <div class="form-group"><label>Valor (R$):</label><input type="number" name="amount" step="0.01" required></div>
                <button type="submit" class="submit-button">Confirmar</button>
            </form>
        </div>
    </div>

    <div id="deleteConfirmModal" class="modal-overlay"> 
    <div class="modal-content">
        
        <div class="modal-header">
            <h2>Confirmar Exclusão</h2>
            <button class="modal-close-btn" id="closeDeleteModal">&times;</button>
        </div>

        <div class="modal-body" style="text-align: center;">
            <p>Tem certeza que deseja excluir a meta: <strong id="delete-meta-name-display" style="color: #f04438;">[Nome da Meta Aqui]</strong>?</p>
            <p>Esta ação não pode ser desfeita.</p>
        </div>

        <form id="deleteConfirmForm">
            <input type="hidden" name="meta_id" id="delete-meta-id-input">
            <div class="modal-footer" style="justify-content: center;">
                <button type="button" style="color: #fff;" class="btn btn-secondary" id="cancelDeleteButton">Cancelar</button>
                <button type="submit" class="btn" style="background-color: #f04438; color: #fff;" id="confirmDeleteButton">
                    Confirmar Exclusão
                </button>
            </div>
        </form>
    </div>
</div>

    <div id="historyModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header"><h2>Histórico: <span id="history-meta-name"></span></h2><button id="closeHistoryModal" class="close-button">&times;</button></div>
            <ul id="history-list" class="history-list"></ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../template/asset/js/Meta.js?v=<?= time() ?>"></script>
</body>
</html>