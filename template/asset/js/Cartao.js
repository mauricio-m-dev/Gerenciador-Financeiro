document.addEventListener('DOMContentLoaded', function () {
    // =========================================================
    // 🎯 DECLARAÇÕES GLOBAIS UNIFICADAS
    // =========================================================
    const navLinks = document.querySelectorAll('.nav-links a');
    const STORAGE_KEY = 'nav-active-index'; // Key para navegação lateral
    
    // Variáveis para o Filtro de Cartão:
    const cardPlaceholders = document.querySelectorAll('.card-placeholder');
    const CARD_SELECT_KEY = 'selected-card-index'; // Key para seleção de cartão
    let expenseChart; // Declarada com 'let' para ser atualizada por outras funções
    let highlightedIndex = null; // Variável para controle de destaque do gráfico

    // Função auxiliar para esmaecer a cor
    function dimColor(hex, alpha) {
        const h = hex.replace('#', '');
        const bigint = parseInt(h, 16);
        const r = (bigint >> 16) & 255;
        const g = (bigint >> 8) & 255;
        const b = bigint & 255;
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    // =========================================================
    // 1. FUNÇÕES DE FILTRO E ATUALIZAÇÃO DO DASHBOARD
    // =========================================================

    // NOVA FUNÇÃO: Atualiza porcentagem e seta ícone UP/DOWN nos cards de sumário
    function updateSummaryPercent(name, percent, trend) {
        const card = document.querySelector(`.${name}.mini-summary-card`);
        if (!card) return;
        
        // Localiza o bloco de indicador de porcentagem (ex: div com a classe .increase-indicator)
        const indicator = card.querySelector('.inner-block.small2');
        if (!indicator) return;

        // Limpa classes antigas de tendência
        indicator.classList.remove('increase-indicator', 'decrease-indicator');
        
        // Define a nova classe de tendência e o ícone
        const arrowIcon = (trend === 'up') 
            ? '<i class=\'bx bx-up-arrow-alt\'></i>' 
            : '<i class=\'bx bx-down-arrow-alt\'></i>';
        
        const trendClass = (trend === 'up') ? 'increase-indicator' : 'decrease-indicator';
        
        // Aplica a nova classe e o conteúdo HTML
        indicator.classList.add(trendClass);
        // O PHP já está enviando a vírgula, mas garantimos que o '.' seja tratado se necessário
        const displayPercent = percent.toString().replace('.', ','); 
        indicator.innerHTML = arrowIcon + ' ' + displayPercent + '%';
    }


    function updateDashboard(data) {
        // 1. Atualiza Cards de Sumário
        document.getElementById('renda-valor').textContent = data.sumario.renda_formatada; 
        document.getElementById('despesas-valor').textContent = data.sumario.despesas_formatada;
        document.getElementById('metas-valor').textContent = data.sumario.metas_formatada;
        
        // 🚨 NOVO: Atualiza Porcentagens e Tendências
        updateSummaryPercent('Renda', data.sumario.renda_percent, data.sumario.renda_trend);
        updateSummaryPercent('Despesas', data.sumario.despesas_percent, data.sumario.despesas_trend);
        updateSummaryPercent('Metas', data.sumario.metas_percent, data.sumario.metas_trend);


        // 2. Atualiza a Tabela de Transações
        const tbody = document.querySelector('.transactions-table tbody');
        if (tbody) {
            tbody.innerHTML = data.tabela_html;
            // Re-adiciona o listener dos "três pontos" para as novas linhas da tabela
            const newDots = document.querySelectorAll('.transactions-table td i.bx-dots-vertical-rounded');
            newDots.forEach(d => {
                d.addEventListener('click', (e) => {
                    const tr = e.target.closest('tr');
                    if (tr) tr.classList.toggle('row-selected');
                });
            });
        }
        
        // 3. Atualiza o Gráfico Doughnut
        if (typeof expenseChart !== 'undefined' && expenseChart && data.grafico) {
            expenseChart.data.labels = data.grafico.labels; 
            expenseChart.data.datasets[0].data = data.grafico.data; 
            
            const baseColors = ['#EF4438', '#9B51E0', '#4CAF50', '#2D9CDB', '#F2C94C'];
            expenseChart.data.datasets[0].backgroundColor = baseColors.slice(0, data.grafico.labels.length);
            
            expenseChart.update(); 
        }

        // 🚨 NOVO: 4. Atualiza a Lista de Categorias e religa os eventos
        const categoryList = document.querySelector('.category-list');
        if (categoryList && data.category_list_html) {
            categoryList.innerHTML = data.category_list_html;
            rebindCategoryListeners();
        }
    }
    
    // NOVO: Função para re-adicionar listeners às novas categorias (mantido e isolado)
    function rebindCategoryListeners() {
        const categoryItems = document.querySelectorAll('.category-item');
        const baseColors = ['#EF4438', '#9B51E0', '#4CAF50', '#2D9CDB', '#F2C94C'];
        
        categoryItems.forEach((li, i) => {
            li.addEventListener('click', () => {
                if (!expenseChart) return;
                
                // Diminui/aumenta o realce no índice clicado
                if (highlightedIndex === i) {
                    // reset
                    expenseChart.data.datasets[0].backgroundColor = baseColors.slice(0, expenseChart.data.labels.length);
                    highlightedIndex = null;
                } else {
                    highlightedIndex = i;
                    const newColors = baseColors.slice(0, expenseChart.data.labels.length).map((c, idx) => {
                        return idx === i ? c : dimColor(c, 0.18);
                    });
                    expenseChart.data.datasets[0].backgroundColor = newColors;
                }
                expenseChart.update();
            });
        });
    }

    function filterDashboardByCardId(cardId) {
        const idParaFiltro = cardId || 0; 
        
        // 🚨 CAMINHO CORRIGIDO NOVAMENTE, assumindo /config/ é o certo
        const url = `../config/filtro_dados.php?cartao_id=${idParaFiltro}`; 

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao buscar dados filtrados do servidor. Status: ' + response.status);
                }
                return response.json(); 
            })
            .then(data => {
                updateDashboard(data);
            })
            .catch(error => {
                console.error('Falha na comunicação com o servidor:', error);
            });
    }

    function setCardActive(index) {
        let cardId = null;

        cardPlaceholders.forEach((el, i) => {
            if (i === index) {
                el.classList.add('active');
                cardId = el.dataset.cardId; // Pega o ID (19 ou 20)
            }
            else {
                el.classList.remove('active');
            }
        });
        localStorage.setItem(CARD_SELECT_KEY, String(index));
        
        filterDashboardByCardId(cardId);
    }
    
    
    // =========================================================
    // 2. INICIALIZAÇÃO DE NAVEGAÇÃO E SCROLL (Mantido)
    // =========================================================

    function setActive(index) {
        navLinks.forEach((a, i) => {
            if (i === index) {
                a.classList.add('active');
                a.setAttribute('aria-current', 'page');
            } else {
                a.classList.remove('active');
                a.removeAttribute('aria-current');
            }
        });
    }
    
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved !== null) {
        // ... (Seu código de scroll automático permanece aqui) ...
        (function() {
            const THRESHOLD = 843;

            function scrollTransactionsIfNarrow() {
                const list = document.querySelector('.transactions-list');
                if (!list) return;
                if (window.innerWidth <= THRESHOLD) {
                    setTimeout(() => {
                        try {
                            list.scrollTo({ top: list.scrollHeight, behavior: 'smooth' });
                        } catch (e) {
                            list.scrollTop = list.scrollHeight;
                        }
                    }, 250);
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', scrollTransactionsIfNarrow);
            } else {
                scrollTransactionsIfNarrow();
            }

            let lastNarrow = window.innerWidth <= THRESHOLD;
            window.addEventListener('resize', () => {
                const nowNarrow = window.innerWidth <= THRESHOLD;
                if (nowNarrow && !lastNarrow) {
                    scrollTransactionsIfNarrow();
                }
                lastNarrow = nowNarrow;
            });
        })();
        
        const idx = parseInt(saved, 10);
        if (!Number.isNaN(idx) && idx >= 0 && idx < navLinks.length) {
            setActive(idx);
        }
    }

    // Click handlers (Navegação lateral)
    navLinks.forEach((link, idx) => {
        link.addEventListener('click', function (e) {
            if (link.getAttribute('href') === '#') e.preventDefault();
            setActive(idx);
            localStorage.setItem(STORAGE_KEY, String(idx));
        });

        link.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                link.click();
            }
        });
    });


    // =========================================================
    // 3. INICIALIZAÇÃO DO GRÁFICO (Chart.js)
    // =========================================================
    const ctx = document.getElementById('expenseDoughnutChart');
    
    if (ctx) {
        let chartLabels = [];
        let chartValores = [];

        try {
            chartLabels = JSON.parse(ctx.dataset.labels || '[]');
            chartValores = JSON.parse(ctx.dataset.valores || '[]');
        } catch (e) {
            console.error("Erro ao ler dados do gráfico (JSON inválido):", e);
            chartValores = [100];
        }

        const data = {
            labels: chartLabels,
            datasets: [{
                label: 'Despesas (R$)',
                data: chartValores,
                backgroundColor: [
                    '#EF4438', '#9B51E0', '#4CAF50', '#2D9CDB', '#F2C94C'
                ],
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 10
            }]
        };

        const config = {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label;
                            }
                        }
                    },
                    datalabels: {
                        display: false
                    }
                }
            }
        };

        if (window.ChartDataLabels) {
            Chart.register(ChartDataLabels);
        }

        const ctx2d = ctx.getContext('2d');
        // ATRIBUIÇÃO CORRIGIDA: Usa a variável global 'expenseChart'
        expenseChart = new Chart(ctx2d, config);

        /* ---------------------------
            Interações adicionais
        --------------------------- */

        // 1) Seleção de Cartão (Filtro AJAX)
        const savedCardIndex = localStorage.getItem(CARD_SELECT_KEY);
        if (savedCardIndex !== null) {
            const idx = parseInt(savedCardIndex, 10);
            if (!Number.isNaN(idx) && idx >= 0 && idx < cardPlaceholders.length) {
                setCardActive(idx); 
            } else {
                if (cardPlaceholders.length > 0) setCardActive(0);
            }
        } else {
            // Define o primeiro cartão como ativo e executa o filtro geral
            if (cardPlaceholders.length > 0) setCardActive(0);
        }

        cardPlaceholders.forEach((el, i) => {
            el.addEventListener('click', () => setCardActive(i));
        });

        // 3) Realce por categoria: Chama a função para ligar os eventos
        // NOTE: Isso deve ser chamado no final da inicialização E após o filtro, 
        // mas a lógica de rebindCategoryListeners está dentro de updateDashboard.
        // O listener de click do cartão em setCardActive já cuida disso.

        // 4) Interação na tabela de transações: (Mantido - para dados iniciais)
        const dots = document.querySelectorAll('.transactions-table td i.bx-dots-vertical-rounded');
        dots.forEach(d => {
            d.addEventListener('click', (e) => {
                const tr = e.target.closest('tr');
                if (tr) tr.classList.toggle('row-selected');
            });
        });
    }

    // =========================================================
    // 4. LÓGICA DO FORMULÁRIO (Máscara, Limite, AJAX) (Mantido)
    // =========================================================
    
    // Máscara para número do cartão (XXXX XXXX XXXX XXXX)
    const numeroCartaoInput = document.getElementById('numeroCartao');
    if (numeroCartaoInput) {
        numeroCartaoInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.match(/.{1,4}/g);
            if(value) e.target.value = value.join(' ');
        });
    }

    // Mostrar/ocultar campo de limite conforme tipo de cartão
    const tipoCartaoSelect = document.getElementById('tipoCartao');
    const limiteInput = document.getElementById('limiteCartao');

    function toggleLimite() {
        if (!limiteInput) return;

        if(tipoCartaoSelect.value === 'credito') {
            limiteInput.required = true;
            limiteInput.parentElement.style.display = 'block';
        } else {
            limiteInput.required = false;
            limiteInput.parentElement.style.display = 'none';
            limiteInput.value = '';
        }
    }
    
    if (tipoCartaoSelect) {
        tipoCartaoSelect.addEventListener('change', toggleLimite);
        toggleLimite(); // inicial
    }

    // Integração AJAX para Envio do Formulário de Cartão (Fetch API)
    const formAddCartao = document.getElementById('formAddCartao');
    const modalAddCartaoElement = document.getElementById('modalAddCartao');
    const modalAddCartao = (typeof bootstrap !== 'undefined' && modalAddCartaoElement) 
                               ? new bootstrap.Modal(modalAddCartaoElement) 
                               : null;

    if (formAddCartao && modalAddCartao) {
        formAddCartao.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const actionUrl = this.getAttribute('action');

            fetch(actionUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.text();
            })
            .then(data => {
                modalAddCartao.hide(); 
                window.location.reload(); 
            })
            .catch(error => {
                console.error("Erro no Servidor/Validação:", error.message);
            });
        });
    }

});