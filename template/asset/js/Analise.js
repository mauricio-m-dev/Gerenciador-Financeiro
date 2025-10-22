// Espera o DOM carregar para garantir que os elementos existam
document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. Funcionalidade do Menu Hamburguer ---
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');

    hamburger.addEventListener('click', () => {
        // Anima o hamburguer (vira "X" ou volta ao normal)
        hamburger.classList.toggle('active');
        
        // Mostra/Esconde o menu dropdown
        navLinks.classList.toggle('active');
    });

    // (Bônus) Fecha o menu ao clicar em um link (útil em Single Page Applications)
    document.querySelectorAll('.nav-links li a').forEach(link => {
        link.addEventListener('click', () => {
            if (navLinks.classList.contains('active')) {
                hamburger.classList.remove('active');
                navLinks.classList.remove('active');
            }
        });
    });


    // --- 2. Diferencial: Sombra no Header ao Rolar ---
    const header = document.querySelector('header');
    
    window.addEventListener('scroll', () => {
        // Adiciona a classe 'scrolled' se o usuário rolar mais de 20 pixels
        if (window.scrollY > 20) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // --- 3. NOVO: Inicialização dos Gráficos com Chart.js ---
    
    function initializeCharts() {
        
        // 3.1. Gráfico de Linha: Evolução Patrimonial
        const evolutionCtx = document.getElementById('evolution-chart').getContext('2d');
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Patrimônio (R$)',
                    data: [30000, 34000, 38000, 41000, 43500, 45000], // Dados de Exemplo
                    borderColor: '#155EEF', // Cor da linha (Azul)
                    backgroundColor: 'rgba(21, 94, 239, 0.1)', // Fundo abaixo da linha
                    tension: 0.3, // Curva da linha (para ficar mais suave)
                    pointRadius: 5, // Tamanho dos pontos
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
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false, // Esconde a legenda
                    },
                    title: {
                        display: false,
                    }
                }
            }
        });

        // 3.2. Gráfico de Rosca (Doughnut): Despesas por Categoria
        const expensesCtx = document.getElementById('expenses-chart').getContext('2d');
        const expensesData = [1500, 1272, 636, 1200, 600]; // Moradia (28%), Alimentação (24%), Outros (12%), Transporte (23%), Compras (12%) = R$5208
        const totalExpenses = expensesData.reduce((a, b) => a + b, 0);

        new Chart(expensesCtx, {
            type: 'doughnut', // Gráfico de Rosca
            data: {
                labels: ['Moradia', 'Alimentação', 'Compras', 'Transporte', 'Outros'],
                datasets: [{
                    data: expensesData,
                    backgroundColor: [
                        '#E82929', // Vermelho - Moradia (28%)
                        '#17B26A', // Verde - Alimentação (24%)
                        '#6B7280', // Cinza - Compras (12%)
                        '#9B59B6', // Roxo - Transporte (23%)
                        '#3498DB' // Azul Claro - Outros (12%)
                    ],
                    hoverOffset: 4,
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%', // Faz o gráfico ser uma Rosca
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    const percentage = ((context.parsed / totalExpenses) * 100).toFixed(0);
                                    label += `${percentage}%`;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

    }

    // Chama a função para desenhar os gráficos quando o DOM estiver pronto
    initializeCharts();


});