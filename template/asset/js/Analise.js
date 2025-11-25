document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. Menu e Header (Seu código original) ---
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    const header = document.querySelector('header');

    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        });
        document.querySelectorAll('.nav-links li a').forEach(link => {
            link.addEventListener('click', () => {
                if (navLinks.classList.contains('active')) {
                    hamburger.classList.remove('active');
                    navLinks.classList.remove('active');
                }
            });
        });
    }

    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // --- 2. Inicialização dos Gráficos (Chart.js) ---
    function initializeCharts() {
        
        // 2.1. Gráfico de Linha: Evolução Patrimonial (Seu Design Original)
        const evolutionCtx = document.getElementById('evolution-chart');
        
        if (evolutionCtx) {
            // Lê dados do PHP
            const labels = JSON.parse(evolutionCtx.dataset.labels || '[]');
            const valores = JSON.parse(evolutionCtx.dataset.valores || '[]');

            new Chart(evolutionCtx, {
                type: 'line', // Mantido LINHA como você queria
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Saldo Mensal (R$)',
                        data: valores,
                        borderColor: '#155EEF', 
                        backgroundColor: 'rgba(21, 94, 239, 0.1)', 
                        tension: 0.3,
                        pointRadius: 5,
                        pointBackgroundColor: '#155EEF',
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: {
                                callback: function(val) { return 'R$ ' + val; }
                            }
                        },
                        x: { grid: { display: false } }
                    },
                    plugins: {
                        legend: { display: false }, // Seu design não tinha legenda
                    }
                }
            });
        }

        // 2.2. Gráfico de Rosca: Despesas por Categoria
        const expensesCtx = document.getElementById('expenses-chart');
        
        if (expensesCtx) {
            const labelsCat = JSON.parse(expensesCtx.dataset.labels || '[]');
            const valoresCat = JSON.parse(expensesCtx.dataset.valores || '[]');
            
            if (valoresCat.length > 0) {
                new Chart(expensesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: labelsCat,
                        datasets: [{
                            data: valoresCat,
                            backgroundColor: [
                                '#E82929', '#17B26A', '#6B7280', '#9B59B6', '#3498DB', '#F59E0B'
                            ],
                            hoverOffset: 4,
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { position: 'right' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let val = context.raw;
                                        let fmt = val.toLocaleString('pt-BR', {style:'currency', currency:'BRL'});
                                        return context.label + ': ' + fmt;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                expensesCtx.parentElement.innerHTML = '<p style="text-align:center; color:#999; padding-top:20%;">Sem despesas.</p>';
            }
        }
    }

    initializeCharts();
});