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
});



// Arquivo: MetaChart.js

// Dados do gráfico: valores acumulados em cada mês (exemplo)
const chartData = {
    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
    datasets: [{
        label: 'Progresso Mensal',
        data: [1000, 2500, 4000, 5200, 7100, 8500], // Acumulado: R$ 8.500 em Junho
        borderColor: '#2a68ff', // A cor azul da sua simulação
        backgroundColor: 'rgba(42, 104, 255, 0.1)', // Cor de fundo abaixo da linha (opcional)
        borderWidth: 2,
        tension: 0.4, // Suaviza a linha, como no seu design
        fill: false,
        pointRadius: 3,
        pointBackgroundColor: '#2a68ff',
    }]
};

// Configurações do gráfico
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false, // Permite que o CSS controle o tamanho do chart-container
    plugins: {
        legend: {
            display: false, // Esconde a legenda "Progresso Mensal"
        },
        tooltip: {
            mode: 'index',
            intersect: false,
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        // Formata o valor como moeda brasileira
                        label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed.y);
                    }
                    return label;
                }
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: {
                // Configuração das linhas horizontais para parecer com seu CSS
                color: '#f0f0f0', 
                borderDash: [5, 5],
            },
            ticks: {
                callback: function(value, index, values) {
                    // Exibe apenas o valor máximo e mínimo para simplificar (opcional)
                    return index === 0 || index === values.length - 1 ? 'R$ ' + value.toLocaleString('pt-BR') : '';
                }
            },
            min: 0,
            max: 12000 // Definindo o objetivo como o máximo do eixo Y
        },
        x: {
            grid: {
                display: false // Esconde as linhas verticais
            }
        }
    }
};

// Encontra o elemento canvas e renderiza o gráfico
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('progressionChart');

    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: chartOptions
        });
    }
});