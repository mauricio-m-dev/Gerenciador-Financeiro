document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('.nav-links a');
    const STORAGE_KEY = 'nav-active-index';

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

    // Restore from localStorage
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved !== null) {
        /*
        |--------------------------------------------------------------------------
        | Scroll automático para telas estreitas
        |--------------------------------------------------------------------------
        */
        (function() {
            const THRESHOLD = 843;

            function scrollTransactionsIfNarrow() {
                const list = document.querySelector('.transactions-list');
                if (!list) return;
                if (window.innerWidth <= THRESHOLD) {
                    // espera um pequeno atraso para garantir layout completo (imagens/fonts)
                    setTimeout(() => {
                        try {
                            list.scrollTo({ top: list.scrollHeight, behavior: 'smooth' });
                        } catch (e) {
                            list.scrollTop = list.scrollHeight;
                        }
                    }, 250);
                }
            }

            // Chama no carregamento
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', scrollTransactionsIfNarrow);
            } else {
                scrollTransactionsIfNarrow();
            }

            // Detecta mudança de tamanho e rola quando cruzar para abaixo do limite
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

    // Click handlers
    navLinks.forEach((link, idx) => {
        link.addEventListener('click', function (e) {
            if (link.getAttribute('href') === '#') e.preventDefault();
            setActive(idx);
            localStorage.setItem(STORAGE_KEY, String(idx));
        });

        // Allow keyboard Enter/Space to activate
        link.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                link.click();
            }
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Seção 2: Gráfico de Despesas (Chart.js)
    |--------------------------------------------------------------------------
    */
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

        console.log('expenseDoughnutChart datasets:', chartLabels, chartValores);
    
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
        const expenseChart = new Chart(ctx2d, config);

        /* ---------------------------
            Interações adicionais
        --------------------------- */

        // 1) Seleção de card placeholders (salva no localStorage)
        const cardPlaceholders = document.querySelectorAll('.card-placeholder');
        const SELECTED_CARD_KEY = 'selected-card-index';

        function setCardActive(index) {
            cardPlaceholders.forEach((el, i) => {
                if (i === index) el.classList.add('active');
                else el.classList.remove('active');
            });
            localStorage.setItem(SELECTED_CARD_KEY, String(index));
        }

        // Restaura seleção
        const savedCard = localStorage.getItem(SELECTED_CARD_KEY);
        if (savedCard !== null) {
            const idx = parseInt(savedCard, 10);
            if (!Number.isNaN(idx) && idx >= 0 && idx < cardPlaceholders.length) setCardActive(idx);
        }

        cardPlaceholders.forEach((el, i) => {
            el.addEventListener('click', () => setCardActive(i));
        });

        
        // 3) Realce por categoria: ao clicar em item da lista de categorias, destaca a fatia correspondente
        const categoryItems = document.querySelectorAll('.category-item');
        const baseColors = [
            '#EF4438', '#9B51E0', '#4CAF50', '#2D9CDB', '#F2C94C'
        ];

        function dimColor(hex, alpha) {
            const h = hex.replace('#', '');
            const bigint = parseInt(h, 16);
            const r = (bigint >> 16) & 255;
            const g = (bigint >> 8) & 255;
            const b = bigint & 255;
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        let highlightedIndex = null;
        categoryItems.forEach((li, i) => {
            li.addEventListener('click', () => {
                if (!expenseChart) return;
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

        // 4) Interação na tabela de transações: destaque da linha ao clicar nos três pontos
        const dots = document.querySelectorAll('.transactions-table td i.bx-dots-vertical-rounded');
        dots.forEach(d => {
            d.addEventListener('click', (e) => {
                const tr = e.target.closest('tr');
                if (!tr) return;
                tr.classList.toggle('row-selected');
            });
        });
    }


    /*
    |--------------------------------------------------------------------------
    | 3. Uso da Seleção: Lógica de Filtro
    |--------------------------------------------------------------------------
    */
    const SELECTED_CARD_KEY = 'selected-card-index'; // Redefinido aqui para evitar erro de escopo

    function filterDashboardBySelectedCard() {
        const savedCardIndex = localStorage.getItem(SELECTED_CARD_KEY); 
        const selectedCard = document.querySelector('.card-placeholder.active'); 
        
        if (!selectedCard) {
            console.log("Nenhum cartão selecionado. Exibindo dados de todas as contas.");
            return; 
        }
        
        const cardId = selectedCard.dataset.cardId || `Card ${savedCardIndex}`;
        
        console.log(`Painel filtrado pelo: ${cardId}`);
    }

    // Chama o filtro uma vez no carregamento da página para refletir o estado salvo:
    filterDashboardBySelectedCard();

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
        if (!limiteInput) return; // Proteção extra

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


    /*
    |--------------------------------------------------------------------------
    | CORREÇÃO: Integração AJAX para Envio do Formulário de Cartão (Fetch API)
    |--------------------------------------------------------------------------
    | Este bloco foi movido para DENTRO do DOMContentLoaded.
    */
    const formAddCartao = document.getElementById('formAddCartao');
    const modalAddCartaoElement = document.getElementById('modalAddCartao');
    // Verifica se a classe Bootstrap está disponível e inicializa o modal
    const modalAddCartao = (typeof bootstrap !== 'undefined' && modalAddCartaoElement) 
                           ? new bootstrap.Modal(modalAddCartaoElement) 
                           : null;

    if (formAddCartao && modalAddCartao) {
        formAddCartao.addEventListener('submit', function(e) {
            // 🛑 CRUCIAL: IMPEDE O ENVIO TRADICIONAL
            e.preventDefault();

            // Coleta os dados do formulário
            const formData = new FormData(this);
            const actionUrl = this.getAttribute('action');

            // Envia os dados usando Fetch
            fetch(actionUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    // Captura e lança o erro retornado pelo PHP (Status 400 ou 500)
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.text();
            })
            .then(data => {
                // SUCESSO
                modalAddCartao.hide(); 
                
                window.location.reload(); 
            })
            .catch(error => {
                // ERRO
                console.error("Erro no Servidor/Validação:", error.message);
                // Exibe a mensagem de erro que veio do PHP
                
            });
        });
    }

});