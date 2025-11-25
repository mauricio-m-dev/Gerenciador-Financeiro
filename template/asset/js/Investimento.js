document.addEventListener('DOMContentLoaded', () => {

    /* ============================================================
       1. MENU E HEADER
       ============================================================ */
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    const header = document.querySelector('header');

    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        });
        
        // Fecha menu ao clicar fora ou em link
        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !navLinks.contains(e.target)) {
                hamburger.classList.remove('active');
                navLinks.classList.remove('active');
            }
        });
    }

    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) header.classList.add('scrolled');
        else header.classList.remove('scrolled');
    });

    /* ============================================================
       2. CALCULADORA DO MODAL
       ============================================================ */
    const qtdInput = document.getElementById('qtd-input');
    const valorUnitInput = document.getElementById('investment-value-unit');
    const valorTotalInput = document.getElementById('investment-value-total');

    function calcularTotal() {
        let qtd = parseFloat(qtdInput.value.replace(',', '.')) || 0;
        let valor = parseFloat(valorUnitInput.value.replace(',', '.')) || 0;
        
        if(valorTotalInput) {
            valorTotalInput.value = (qtd * valor).toFixed(2).replace('.', ',');
        }
    }

    if (qtdInput && valorUnitInput) {
        qtdInput.addEventListener('input', calcularTotal);
        valorUnitInput.addEventListener('input', calcularTotal);
    }

    /* ============================================================
       3. GRÁFICO
       ============================================================ */
    const ctx = document.getElementById('expenseDoughnutChart');
    if (ctx) {
        try {
            const labels = JSON.parse(ctx.dataset.labels || '[]');
            const valores = JSON.parse(ctx.dataset.valores || '[]');

            if (labels.length > 0) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: valores,
                            backgroundColor: ['#155EEF', '#17B26A', '#F79009', '#F04438', '#2E294E', '#38B6FF'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { 
                                position: 'bottom',
                                labels: { usePointStyle: true, padding: 20, font: { family: "'Poppins', sans-serif" } }
                            }
                        }
                    }
                });
            } else {
                ctx.style.display = 'none';
                ctx.parentElement.innerHTML = '<p style="text-align:center; color:#999; margin-top:40%;">Sem dados.</p>';
            }
        } catch (e) { console.error("Erro gráfico:", e); }
    }

    /* ============================================================
       4. AUTOCOMPLETE (Busca no Modal)
       ============================================================ */
    const stockInput = document.getElementById('stock-search');
    const suggestionsBox = document.getElementById('stock-suggestions');
    const hiddenSymbol = document.getElementById('stock-symbol');
    const hiddenName = document.getElementById('stock-name');
    const TOKEN_BRAPI = 'pz4ZsbSAgwP96rCKvp52Pq'; 
    let timer;

    if (stockInput && suggestionsBox) {
        stockInput.addEventListener('input', function() {
            const term = this.value.toUpperCase().trim();
            clearTimeout(timer);
            suggestionsBox.style.display = 'none';

            if (term.length < 2) return;

            timer = setTimeout(async () => {
                try {
                    const res = await fetch(`https://brapi.dev/api/quote/list?search=${term}&limit=5&token=${TOKEN_BRAPI}`);
                    const data = await res.json();

                    if (data.stocks) {
                        suggestionsBox.innerHTML = '';
                        suggestionsBox.style.display = 'block';
                        
                        data.stocks.forEach(s => {
                            const div = document.createElement('div');
                            div.className = 'autocomplete-item';
                            div.innerHTML = `<strong>${s.stock}</strong> - ${s.name}`;
                            div.onclick = () => {
                                stockInput.value = `${s.stock} - ${s.name}`;
                                if(hiddenSymbol) hiddenSymbol.value = s.stock;
                                if(hiddenName) hiddenName.value = s.name;
                                if(valorUnitInput) {
                                    valorUnitInput.value = s.close.toFixed(2);
                                    calcularTotal();
                                }
                                suggestionsBox.style.display = 'none';
                            };
                            suggestionsBox.appendChild(div);
                        });
                    }
                } catch(e) { console.log(e); }
            }, 300);
        });

        document.addEventListener('click', e => {
            if(suggestionsBox && !stockInput.contains(e.target)) suggestionsBox.style.display = 'none';
        });
    }

    /* ============================================================
       5. CARREGAR MERCADO (ALEATÓRIO & BONITO)
       ============================================================ */
    const marketContainer = document.getElementById('market-cards');
    
    if (marketContainer) {
        async function carregarMercado() {
            try {
                // 1. Pedimos 60 ações em vez de 10 para ter variedade
                const url = `https://brapi.dev/api/quote/list?limit=60&token=${TOKEN_BRAPI}`;
                const response = await fetch(url);
                const data = await response.json();

                if (data.stocks) {
                    marketContainer.innerHTML = ''; 
                    
                    // 2. Embaralhamos a lista de 60 ações (Randomização)
                    const acoesEmbaralhadas = data.stocks.sort(() => 0.5 - Math.random());

                    // 3. Pegamos apenas as 10 primeiras da lista embaralhada
                    const acoesSelecionadas = acoesEmbaralhadas.slice(0, 10);
                    
                    // 4. Renderizamos com o design bonito
                    acoesSelecionadas.forEach(stock => {
                        const valor = stock.close.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                        
                        const card = document.createElement('div');
                        card.className = 'market-card'; 
                        
                        card.innerHTML = `
                            <div style="display: flex; flex-direction: column; height: 100%; justify-content: space-between;">
                                <div>
                                    <div style="font-weight: 800; font-size: 1.1rem; color: #101828; margin-bottom: 2px;">
                                        ${stock.stock}
                                    </div>
                                    <div style="font-size: 0.8rem; color: #667085; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                        ${stock.name}
                                    </div>
                                </div>
                                <div style="margin-top: 12px; font-weight: 700; color: #155eef; font-size: 1rem;">
                                    ${valor}
                                </div>
                            </div>
                        `;
                        marketContainer.appendChild(card);
                    });
                }
            } catch (error) {
                console.error("Erro ao carregar mercado:", error);
                marketContainer.innerHTML = '<p style="color:#999; width:100%; text-align:center;">Falha na conexão com Brapi.</p>';
            }
        }
        carregarMercado();
    }
}); // Fim do DOMContentLoaded