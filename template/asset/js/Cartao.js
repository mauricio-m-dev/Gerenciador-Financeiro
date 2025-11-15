document.addEventListener('DOMContentLoaded', function () {
    // =========================================================
    // 🎯 DECLARAÇÕES GLOBAIS UNIFICADAS
    // =========================================================
    const navLinks = document.querySelectorAll('.nav-links a');
    const STORAGE_KEY = 'nav-active-index';

    // Filtro de Cartão:
    const cardPlaceholders = document.querySelectorAll('.card-placeholder');
    const CARD_SELECT_KEY = 'selected-card-index';
    let expenseChart = null;
    let highlightedIndex = null;

    // Cores base (use mais cores se precisar)
    const BASE_COLORS = ['#EF4438', '#9B51E0', '#4CAF50', '#2D9CDB', '#F2C94C', '#F2994A', '#6C5CE7'];

    // =========================================================
    // Helpers
    // =========================================================
    function dimColor(hex, alpha) {
        if (!hex) return `rgba(0,0,0,${alpha})`;
        const h = hex.replace('#', '');
        const bigint = parseInt(h, 16);
        const r = (bigint >> 16) & 255;
        const g = (bigint >> 8) & 255;
        const b = bigint & 255;
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    function safeParseFloatArray(arr) {
        if (!Array.isArray(arr)) return [];
        return arr.map(v => {
            const n = parseFloat(v);
            return Number.isFinite(n) ? n : 0;
        });
    }

    // =========================================================
    // 1. FUNÇÕES DE FILTRO E ATUALIZAÇÃO DO DASHBOARD
    // =========================================================
    function updateSummaryPercent(name, percent, trend) {
        const card = document.querySelector(`.${name}.mini-summary-card`);
        if (!card) return;
        const indicator = card.querySelector('.inner-block.small2');
        if (!indicator) return;

        indicator.classList.remove('increase-indicator', 'decrease-indicator');

        const arrowIcon = (trend === 'up')
            ? '<i class="bx bx-up-arrow-alt"></i>'
            : '<i class="bx bx-down-arrow-alt"></i>';

        const trendClass = (trend === 'up') ? 'increase-indicator' : 'decrease-indicator';

        indicator.classList.add(trendClass);
        const displayPercent = (typeof percent === 'number') ? percent.toFixed(2).replace('.', ',') : String(percent).replace('.', ',');
        indicator.innerHTML = arrowIcon + ' ' + displayPercent + '%';
    }

    function rebindCategoryListeners() {
        const categoryItems = document.querySelectorAll('.category-item');
        if (!categoryItems) return;

        categoryItems.forEach((li, i) => {
            // remove any existing to avoid duplicate bindings (idempotência)
            li.replaceWith(li.cloneNode(true));
        });

        const freshItems = document.querySelectorAll('.category-item');

        freshItems.forEach((li, i) => {
            li.addEventListener('click', () => {
                if (!expenseChart) return;

                if (highlightedIndex === i) {
                    // reset
                    expenseChart.data.datasets[0].backgroundColor = BASE_COLORS.slice(0, expenseChart.data.labels.length);
                    highlightedIndex = null;
                } else {
                    highlightedIndex = i;
                    const newColors = BASE_COLORS.slice(0, expenseChart.data.labels.length).map((c, idx) => {
                        return idx === i ? c : dimColor(c, 0.18);
                    });
                    expenseChart.data.datasets[0].backgroundColor = newColors;
                }
                expenseChart.update();
            });
        });
    }

    function updateDashboard(data) {
        // 1. Cards
        if (data.sumario) {
            if (document.getElementById('renda-valor')) document.getElementById('renda-valor').textContent = data.sumario.renda_formatada || '';
            if (document.getElementById('despesas-valor')) document.getElementById('despesas-valor').textContent = data.sumario.despesas_formatada || '';
            if (document.getElementById('metas-valor')) document.getElementById('metas-valor').textContent = data.sumario.metas_formatada || '';

            updateSummaryPercent('Renda', data.sumario.renda_percent || 0, data.sumario.renda_trend || 'up');
            updateSummaryPercent('Despesas', data.sumario.despesas_percent || 0, data.sumario.despesas_trend || 'down');
            updateSummaryPercent('Metas', data.sumario.metas_percent || 0, data.sumario.metas_trend || 'up');
        }

        // 2. Tabela
        const tbody = document.querySelector('.transactions-table tbody');
        if (tbody && data.tabela_html !== undefined) {
            tbody.innerHTML = data.tabela_html;
            // rebind dots
            const newDots = document.querySelectorAll('.transactions-table td i.bx-dots-vertical-rounded');
            newDots.forEach(d => {
                d.addEventListener('click', (e) => {
                    const tr = e.target.closest('tr');
                    if (tr) tr.classList.toggle('row-selected');
                });
            });
        }

        // 3. Gráfico
        if (expenseChart && data.grafico) {
            const labels = Array.isArray(data.grafico.labels) ? data.grafico.labels : [];
            const rawData = Array.isArray(data.grafico.data) ? data.grafico.data : [];
            const numericData = safeParseFloatArray(rawData);

            expenseChart.data.labels = labels;
            expenseChart.data.datasets[0].data = numericData;
            expenseChart.data.datasets[0].backgroundColor = BASE_COLORS.slice(0, Math.max(1, labels.length));
            expenseChart.update();
        }

        // 4. Lista de categorias
        const categoryList = document.querySelector('.category-list');
        if (categoryList && data.category_list_html !== undefined) {
            categoryList.innerHTML = data.category_list_html;
            rebindCategoryListeners();
        }
    }

    function filterDashboardByCardId(cardId) {
        const idParaFiltro = cardId || 0;
        const url = `../config/filtro_dados.php?cartao_id=${idParaFiltro}`;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Erro ao buscar dados filtrados. Status: ' + response.status);
                return response.json();
            })
            .then(data => {
                // certifica-se que grafico.data são números
                if (data && data.grafico && Array.isArray(data.grafico.data)) {
                    data.grafico.data = safeParseFloatArray(data.grafico.data);
                }
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
                cardId = el.dataset.cardId || null;
            } else {
                el.classList.remove('active');
            }
        });

        localStorage.setItem(CARD_SELECT_KEY, String(index));
        filterDashboardByCardId(cardId);
    }

    // =========================================================
    // 2. NAVEGAÇÃO E SCROLL
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
        (function () {
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
                if (nowNarrow && !lastNarrow) scrollTransactionsIfNarrow();
                lastNarrow = nowNarrow;
            });
        })();

        const idx = parseInt(saved, 10);
        if (!Number.isNaN(idx) && idx >= 0 && idx < navLinks.length) {
            setActive(idx);
        }
    }

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
            chartLabels = [];
            chartValores = [100];
        }

        // converte para números (essencial)
        chartValores = safeParseFloatArray(chartValores);

        const data = {
            labels: chartLabels,
            datasets: [{
                label: 'Despesas (%)',
                data: chartValores,
                backgroundColor: BASE_COLORS.slice(0, Math.max(1, chartLabels.length)),
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
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label || '';
                                const value = (context.parsed !== undefined) ? Number(context.parsed) : 0;
                                return label + ': ' + value.toFixed(2) + '%';
                            }
                        }
                    },
                    datalabels: { display: false }
                }
            }
        };

        if (window.ChartDataLabels) Chart.register(ChartDataLabels);

        const ctx2d = ctx.getContext('2d');
        expenseChart = new Chart(ctx2d, config);

        // Inicializa listeners relacionados ao gráfico e à UI
        // Seleção de cartão (filtro AJAX)
        const savedCardIndex = localStorage.getItem(CARD_SELECT_KEY);
        if (savedCardIndex !== null) {
            const idx = parseInt(savedCardIndex, 10);
            if (!Number.isNaN(idx) && idx >= 0 && idx < cardPlaceholders.length) {
                setCardActive(idx);
            } else {
                if (cardPlaceholders.length > 0) setCardActive(0);
            }
        } else {
            if (cardPlaceholders.length > 0) setCardActive(0);
        }

        cardPlaceholders.forEach((el, i) => {
            el.addEventListener('click', () => setCardActive(i));
        });

        // Rebind inicial das categorias (se houver)
        rebindCategoryListeners();

        // Interação na tabela de transações
        const dots = document.querySelectorAll('.transactions-table td i.bx-dots-vertical-rounded');
        dots.forEach(d => {
            d.addEventListener('click', (e) => {
                const tr = e.target.closest('tr');
                if (tr) tr.classList.toggle('row-selected');
            });
        });
    }

    // =========================================================
    // 4. LÓGICA DO FORMULÁRIO (Máscara, Limite, AJAX)
    // =========================================================
    const numeroCartaoInput = document.getElementById('numeroCartao');
    if (numeroCartaoInput) {
        numeroCartaoInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.match(/.{1,4}/g);
            if (value) e.target.value = value.join(' ');
        });
    }

    const tipoCartaoSelect = document.getElementById('tipoCartao');
    const limiteInput = document.getElementById('limiteCartao');

    function toggleLimite() {
        if (!limiteInput) return;
        if (tipoCartaoSelect.value === 'credito') {
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
        toggleLimite();
    }

    const formAddCartao = document.getElementById('formAddCartao');
    const modalAddCartaoElement = document.getElementById('modalAddCartao');
    const modalAddCartao = (typeof bootstrap !== 'undefined' && modalAddCartaoElement) ? new bootstrap.Modal(modalAddCartaoElement) : null;

    if (formAddCartao && modalAddCartao) {
        formAddCartao.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const actionUrl = this.getAttribute('action');

            fetch(actionUrl, { method: 'POST', body: formData })
                .then(response => {
                    if (!response.ok) return response.text().then(text => { throw new Error(text) });
                    return response.text();
                })
                .then(() => {
                    modalAddCartao.hide();
                    window.location.reload();
                })
                .catch(error => {
                    console.error("Erro no Servidor/Validação:", error.message);
                });
        });
    }

    // Click cards que redirecionam com card_id
    const cardElements = document.querySelectorAll('.card-carteira');
    cardElements.forEach(card => {
        card.addEventListener('click', function () {
            const cardId = this.getAttribute('data-card-id');
            if (!cardId) return;
            window.location.href = window.location.pathname + '?card_id=' + cardId;
        });
    });

    // =========================================================
    // 5. MODAL DE DELETAR CARTÃO (AJAX)
    // =========================================================
    const confirmDeleteCardBtn = document.getElementById('confirmDeleteCardBtn');
    const selectCardToDelete = document.getElementById('selectCardToDelete');
    const modalDeleteCartaoElement = document.getElementById('modalDeleteCartao');
    const modalDeleteCartao = (typeof bootstrap !== 'undefined' && modalDeleteCartaoElement) ? new bootstrap.Modal(modalDeleteCartaoElement) : null;

    if (confirmDeleteCardBtn && selectCardToDelete && modalDeleteCartao) {
        confirmDeleteCardBtn.addEventListener('click', function () {
            const cardIdToDelete = selectCardToDelete.value;
            if (!cardIdToDelete || cardIdToDelete === "") {
                console.warn("Nenhum cartão selecionado para deletar.");
                return;
            }

            fetch('../config/deletar_cartao.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_cartao=${cardIdToDelete}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modalDeleteCartao.hide();
                        window.location.reload();
                    } else {
                        console.error('Falha ao deletar cartão:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro na comunicação AJAX:', error);
                });
        });
    }
});
