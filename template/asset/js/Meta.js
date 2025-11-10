// Arquivo: MetaChart.js

const createChartData = (label, dataValues, borderColor, maxGoal) => ({
  labels: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun"],
  datasets: [
    {
      label: label,
      data: dataValues,
      borderColor: borderColor,
      backgroundColor: `${borderColor
        .replace(")", ", 0.1)")
        .replace("rgb", "rgba")}`,
      borderWidth: 2,
      tension: 0.4,
      fill: false,
      pointRadius: 3,
      pointBackgroundColor: borderColor,
    },
  ],
  maxGoal: maxGoal,
});

// Dados dos 4 Gráficos (Baseados nos valores do PHP)
const ChartEmergencia = createChartData(
  "Fundo de Emergência",
  [1000, 2500, 4000, 5200, 7100, 8500],
  "#2a68ff",
  12000
);
const ChartViagem = createChartData(
  "Viagem para Europa",
  [500, 1200, 2000, 2800, 3500, 4200],
  "#FF9800",
  15000
); // Laranja
const ChartApartamento = createChartData(
  "Entrada do Apartamento",
  [5000, 10000, 17500, 25000, 30000, 35000],
  "#4CAF50",
  80000
); // Verde
const ChartCarro = createChartData(
  "Novo Carro",
  [2000, 4000, 6000, 8500, 10000, 12000],
  "#9C27B0",
  40000
); // Roxo

// Configurações do gráfico
const createChartOptions = (maxGoal) => ({
  responsive: true,
  maintainAspectRatio: false, // Permite que o CSS controle o tamanho do chart-container
  plugins: {
    legend: {
      display: false, // Esconde a legenda "Progresso Mensal"
    },
    tooltip: {
      mode: "index",
      intersect: false,
      callbacks: {
        label: function (context) {
          let label = context.dataset.label || "";
          if (label) {
            label += ": ";
          }
          if (context.parsed.y !== null) {
            // Formata o valor como moeda brasileira
            label += new Intl.NumberFormat("pt-BR", {
              style: "currency",
              currency: "BRL",
            }).format(context.parsed.y);
          }
          return label;
        },
      },
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: {
        // Configuração das linhas horizontais para parecer com seu CSS
        color: "#f0f0f0",
        borderDash: [5, 5],
      },
      ticks: {
        callback: function (value, index, values) {
          // Exibe apenas o valor máximo e mínimo para simplificar (opcional)
          return index === 0 || index === values.length - 1
            ? "R$ " + value.toLocaleString("pt-BR")
            : "";
        },
      },
      min: 0,
      // max: 12000 // Definindo o objetivo como o máximo do eixo Y
    },
    x: {
      grid: {
        display: false, // Esconde as linhas verticais
      },
    },
  },
});

// Encontra o elemento canvas e renderiza o gráfico
const createChart = (canvasId, chartData) => {
  const ctx = document.getElementById(canvasId);
  if (ctx) {
    new Chart(ctx, {
      type: "line",
      data: chartData,
      options: createChartOptions(chartData.maxGoal),
    });
  }
};

// Espera o DOM carregar para garantir que os elementos existam
document.addEventListener("DOMContentLoaded", () => {
  // --- 1. Funcionalidade do Menu Hamburguer ---
  const hamburger = document.querySelector(".hamburger");
  const navLinks = document.querySelector(".nav-links");

  hamburger.addEventListener("click", () => {
    // Anima o hamburguer (vira "X" ou volta ao normal)
    hamburger.classList.toggle("active");

    // Mostra/Esconde o menu dropdown
    navLinks.classList.toggle("active");
  });

  // (Bônus) Fecha o menu ao clicar em um link (útil em Single Page Applications)
  document.querySelectorAll(".nav-links li a").forEach((link) => {
    link.addEventListener("click", () => {
      if (navLinks.classList.contains("active")) {
        hamburger.classList.remove("active");
        navLinks.classList.remove("active");
      }
    });
  });

  // --- 2. Diferencial: Sombra no Header ao Rolar ---
  const header = document.querySelector("header");

  window.addEventListener("scroll", () => {
    // Adiciona a classe 'scrolled' se o usuário rolar mais de 20 pixels
    if (window.scrollY > 20) {
      header.classList.add("scrolled");
    } else {
      header.classList.remove("scrolled");
    }
  });

  // --- 3. Inicialização dos Gráficos (RE-IMPLEMENTADO E CORRIGIDO) ---
  // Certifique-se de que os nomes das funções createChartData, createChartOptions e createChart
  // estão definidas *antes* ou *fora* deste bloco DOMContentLoaded.

  // CRÍTICO: Chamar a função de criação do gráfico para cada ID.
  createChart("chartEmergencia", ChartEmergencia);
  createChart("chartViagem", ChartViagem);
  createChart("chartApartamento", ChartApartamento);
  createChart("chartCarro", ChartCarro);

  const addMetaButton = document.querySelector(".add-meta-button"); // DEVE SER ESTE SELETOR!
  const modal = document.getElementById("addMetaModal");
  const closeModalButton = document.getElementById("closeModal");

  const openModal = () => {
    // Usa JS para forçar o display: flex ANTES de adicionar a classe open
    if (modal) {
      modal.style.display = "flex";
      setTimeout(() => {
        modal.classList.add("open");
      }, 10);
    }
  };

  const closeModal = () => {
    if (modal) {
      modal.classList.remove("open");
      // Esconde o modal APÓS a transição (300ms do CSS)
      setTimeout(() => {
        modal.style.display = "none";
      }, 300);
    }
  };

  // CRÍTICO: ANEXAR OS EVENTOS
  if (addMetaButton && modal && closeModalButton) {
    addMetaButton.addEventListener("click", openModal);
    closeModalButton.addEventListener("click", closeModal);

    // Evento de fechar ao clicar no fundo
    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        closeModal();
      }
    });
  } else {
    // Se este aviso aparecer no console, o seletor está errado
    console.error(
      "Erro: Elementos do Modal (Botão ou Modal principal) não encontrados no DOM."
    );
  }
});
