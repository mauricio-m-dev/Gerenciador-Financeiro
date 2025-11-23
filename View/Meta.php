<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>gerenciador - Metas</title>

    <link rel="stylesheet" href="template/asset/css/global.css">
    <link rel="stylesheet" href="template/asset/css/Meta.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
                <li><a href="View/VisaoGeral.php">Visão Geral</a></li>
                <li><a href="View/Investimento.php">Investimentos</a></li>
                <li><a href="View/Analise.php">Análise</a></li>
                <li><a href="metas.php" class="active">Metas</a></li>
                <li><a href="View/Cartao.php">Cartões</a></li>
            </ul>

            <div class="user-area">
                <a href="#" class="settings-icon" aria-label="Configurações">
                    <i class='bx bx-cog'></i> </a>
                <img src="https://via.placeholder.com/40" alt="Foto de Perfil" class="profile-pic">
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="header-meta-section">
                <h1>Metas Financeiras</h1>
                <button class="add-meta-button">
                    <i class='bx bxs-plus'></i>Adicionar Meta</button>
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
                <?php
                if (empty($metas)): ?>
                    <p style="text-align: center; width: 100%; font-size: 1.2rem; color: #777;">Nenhuma meta encontrada no
                        banco de dados. Adicione uma nova!</p>
                <?php endif;

                foreach ($metas as $meta):
                    // Variáveis calculadas no Controller e usadas aqui
                    $progresso = $controller->calcular_progresso($meta['acumulado'], $meta['objetivo']);
                    $falta = $meta['objetivo'] - $meta['acumulado'];
                    $prazo_formatado = $controller->formatar_data($meta['prazo']);
                    $meses_restantes = $controller->calcular_diferenca_meses($meta['prazo']);
                    $cor_meta = isset($meta['cor']) && !empty($meta['cor']) ? $meta['cor'] : '#2a68ff';
                    $historico_js = json_encode(isset($meta['historico_json']) && !empty($meta['historico_json']) ? json_decode($meta['historico_json']) : [$meta['acumulado']]);
                    ?>

                    <div class="card-details" data-meta-id="<?php echo $meta['id']; ?>"
                        data-meta-goal="<?php echo $meta['objetivo']; ?>"
                        data-meta-history='<?php echo htmlspecialchars($historico_js, ENT_QUOTES, 'UTF-8'); ?>'>

                        <div class="card-details-header">
                            <div class="goal-info">
                                <h2><?php echo $meta['nome']; ?></h2>
                                <p><?php echo $meta['categoria']; ?></p>
                            </div>
                            <div class="goal-percentage"><?php echo $progresso; ?>%</div>
                        </div>

                        <div class="progress-bar-container">
                            <div class="progress-bar"
                                style="width: <?php echo $progresso; ?>%; background-color: <?php echo $cor_meta; ?>;">
                            </div>
                        </div>

                        <div class="chart-and-summary">
                            <div class="chart-container">
                                <canvas id="chartMeta-<?php echo $meta['id']; ?>"></canvas>
                            </div>

                            <div class="summary-details-vertical">
                                <div class="summary-item">
                                    <i class='bx bx-dollar'></i> <span>Acumulado:
                                        <b><?php echo $controller->formatar_moeda($meta['acumulado']); ?></b></span>
                                </div>
                                <div class="summary-item">
                                    <i class='bx bx-target-lock'></i> <span>Objetivo:
                                        <b><?php echo $controller->formatar_moeda($meta['objetivo']); ?></b></span>
                                </div>

                                <div class="card-actions">
                                    <button class="contribute-button" data-meta-id="<?php echo $meta['id']; ?>"
                                        data-meta-name="<?php echo $meta['nome']; ?>">
                                        Registrar Aporte
                                    </button>

                                    <button class="history-button" data-meta-id="<?php echo $meta['id']; ?>"
                                        data-meta-name="<?php echo htmlspecialchars($meta['nome'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-meta-history='<?php echo htmlspecialchars($historico_js, ENT_QUOTES, 'UTF-8'); ?>'>
                                        <i class='bx bx-history'></i> Histórico
                                    </button>

                                    <button class="delete-meta-button" data-meta-id="<?php echo $meta['id']; ?>"
                                        data-meta-name="<?php echo htmlspecialchars($meta['nome'], ENT_QUOTES, 'UTF-8'); ?>">
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

                        <div class="alert-message">
                            <i class='bx bxs-error-circle'></i>
                            <div>
                                <span>Faltam <b><?php echo $controller->formatar_moeda($falta); ?></b></span>
                                <p>Continue contribuindo para alcançar sua meta!</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>


            <div class="tips-section">
                <h2>Dicas Financeiras</h2>
                <p>Pilares para uma vida financeira saudável</p>

                <div class="tip-card tip-info">
                    <span class="tip-title-info">
                        • A Base de Tudo
                    </span>
                    <p>Antes de focar em grandes investimentos, garanta sua <strong>Reserva de Emergência</strong>. O
                        ideal é ter guardado de 3 a 6 meses do seu custo de vida em um local seguro e de fácil resgate.
                    </p>
                </div>

                <div class="tip-card tip-success">
                    <span class="tip-title-success">
                        • O Poder da Constância
                    </span>
                    <p>Não importa o valor: aportar todo mês cria o hábito e ativa o efeito dos juros compostos. A
                        consistência vence a intensidade no longo prazo.</p>
                </div>

                <div class="tip-card tip-info">
                    <span class="tip-title-info">
                        • A Regra 50-30-20
                    </span>
                    <p>Uma boa estratégia de divisão de renda: 50% para gastos essenciais, 30% para estilo de vida e
                        desejos, e <strong>20% para suas Metas Financeiras</strong>.</p>
                </div>
            </div>
        </div>
    </main>


    <div id="addMetaModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-title">Adicionar Nova Meta</h2>
                <button id="closeModal" class="close-button" aria-label="Fechar Modal">&times;</button>
            </div>

            <form id="newMetaForm">
                <div class="form-group" id="group-nome">
                    <label for="meta-nome">Nome da Meta:</label>
                    <input type="text" id="meta-nome" name="nome" required placeholder="Ex: Fundo de Emergência">
                </div>

                <div class="form-group" id="group-objetivo">
                    <label for="meta-objetivo">Valor Objetivo (R$):</label>
                    <input type="number" id="meta-objetivo" name="objetivo" required placeholder="Ex: 12000.00"
                        min="0.01" step="0.01">
                </div>

                <div class="form-group" id="group-mensal">
                    <label for="meta-mensal">Contribuição Mensal (R$):</label>
                    <input type="number" id="meta-mensal" name="mensal" required placeholder="Ex: 850.00" min="0.01"
                        step="0.01">
                </div>

                <div class="form-group" id="group-prazo">
                    <label for="meta-prazo">Prazo Final:</label>
                    <input type="date" id="meta-prazo" name="prazo" required min="<?php echo $min_date; ?>">
                </div>

                <div class="form-group" id="group-categoria">
                    <label for="meta-categoria">Categoria:</label>
                    <select id="meta-categoria" name="categoria" required>
                        <option value="" disabled selected>Selecione a Categoria</option>
                        <option value="seguranca">Segurança</option>
                        <option value="lazer">Lazer</option>
                        <option value="moradia">Moradia</option>
                        <option value="veiculo">Veículo</option>
                        <option value="educacao">Educação</option>
                    </select>
                </div>

                <div class="form-group" id="group-cor">
                    <label for="meta-cor">Cor de Exibição (Hex):</label>
                    <input type="color" id="meta-cor" name="cor" value="#2a68ff" required>
                </div>


                <button type="submit" class="submit-button">Criar Meta</button>
            </form>
        </div>
    </div>



    <div id="contributeModal" class="modal-overlay" role="dialog" aria-modal="true"
        aria-labelledby="contribute-modal-title">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="contribute-modal-title">Registrar Aporte para <span id="meta-name-display"></span></h2>
                <button id="closeContributeModal" class="close-button" aria-label="Fechar Modal">&times;</button>
            </div>

            <form id="contributeForm">
                <input type="hidden" id="meta-id-input" name="meta_id" value="">

                <div class="form-group">
                    <label for="contribution-date">Mês e Ano do Aporte:</label>
                    <input type="month" id="contribution-date" name="date" required
                        value="<?php echo date('Y-m'); // Define o mês atual como padrão ?>">
                </div>

                <div class="form-group">
                    <label for="contribution-amount">Valor do Aporte (R$):</label>
                    <input type="number" id="contribution-amount" name="amount" required placeholder="Ex: 500.00"
                        min="0.01" step="0.01">
                </div>

                <button type="submit" class="submit-button">Confirmar Aporte</button>
            </form>
        </div>
    </div>


    <div id="deleteConfirmModal" class="modal-overlay" role="dialog" aria-modal="true"
        aria-labelledby="delete-modal-title">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="delete-modal-title">Confirmar Exclusão</h2>
                <button id="closeDeleteModal" class="close-button" aria-label="Fechar Modal">&times;</button>
            </div>

            <form id="deleteConfirmForm">
                <input type="hidden" id="delete-meta-id-input" value="">

                <p style="font-size: 1.1rem; line-height: 1.5; margin-bottom: 2rem;">
                    Você tem certeza que deseja excluir permanentemente a meta:
                    <br>
                    <b id="delete-meta-name-display" style="color: #d9534f;"></b>?
                    <br><br>
                    <span style="font-weight: 600; color: #333;">Esta ação não pode ser desfeita.</span>
                </p>

                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1rem;">
                    <button type="button" id="cancelDeleteButton" class="submit-button"
                        style="background-color: #6c757d; --hover-color: #5a6268;">
                        Cancelar
                    </button>
                    <button type="submit" id="confirmDeleteButton" class="submit-button"
                        style="background-color: #d9534f; --hover-color: #c9302c;">
                        Confirmar Exclusão
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="historyModal" class="modal-overlay" role="dialog" aria-modal="true">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="history-modal-title">Histórico de: <span id="history-meta-name"></span></h2>
                <button id="closeHistoryModal" class="close-button">&times;</button>
            </div>

            <p style="color: #666; margin-bottom: 1rem; font-size: 0.9rem;">
                Aqui você pode visualizar a evolução e remover lançamentos incorretos.
                <br><b>Atenção:</b> Ao excluir um item, o saldo total será recalculado.
            </p>

            <div class="history-list-container">
                <ul id="history-list" class="history-list">
                </ul>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="template/asset/js/Meta.js"></script>
</body>

</html>