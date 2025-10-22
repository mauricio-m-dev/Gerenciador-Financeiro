/*
|--------------------------------------------------------------------------
| VisaoGeral.js
|--------------------------------------------------------------------------
| Este script é executado quando o DOM está totalmente carregado.
| Ele gerencia todas as interatividades da página Visão Geral.
*/
document.addEventListener("DOMContentLoaded", () => {

  /*
  |--------------------------------------------------------------------------
  | Seção 1: Navbar e Interações do Header
  |--------------------------------------------------------------------------
  */
  
  // Gerencia o menu hamburguer em telas móveis
  const hamburger = document.querySelector(".hamburger");
  const navLinks = document.querySelector(".nav-links");

  if (hamburger && navLinks) {
    hamburger.addEventListener("click", () => {
      hamburger.classList.toggle("active");
      navLinks.classList.toggle("active");
    });

    // Fecha o menu ao clicar em um link
    document.querySelectorAll(".nav-links li a").forEach((link) => {
      link.addEventListener("click", () => {
        if (navLinks.classList.contains("active")) {
          hamburger.classList.remove("active");
          navLinks.classList.remove("active");
        }
      });
    });
  }

  // Adiciona sombra ao header durante a rolagem
  const header = document.querySelector("header");

  if (header) {
    window.addEventListener("scroll", () => {
      if (window.scrollY > 20) {
        header.classList.add("scrolled");
      } else {
        header.classList.remove("scrolled");
      }
    });
  }

  /*
  |--------------------------------------------------------------------------
  | Seção 2: Gráfico de Despesas (Chart.js)
  |--------------------------------------------------------------------------
  */
  const ctx = document.getElementById('expenseDoughnutChart');
  
  if (ctx) {
    let chartLabels = [];
    let chartValores = [];

    // Tenta ler os dados dinâmicos injetados pelo PHP
    try {
      chartLabels = JSON.parse(ctx.dataset.labels);
      chartValores = JSON.parse(ctx.dataset.valores);
    } catch (e) {
      console.error("Erro ao ler dados do gráfico (JSON inválido):", e);
      chartLabels = ['Erro ao carregar'];
      chartValores = [100];
    }
  
    // Configuração dos dados do gráfico
    const data = {
      labels: chartLabels,
      datasets: [{
        label: 'Despesas (R$)',
        data: chartValores,
        backgroundColor: [
          '#F56565', '#155EEF', '#48BB78', '#ED8936', '#718096'
        ],
        borderColor: '#ffffff',
        borderWidth: 3,
        hoverOffset: 10
      }]
    };
  
    // Configuração de opções do gráfico
    const config = {
      type: 'doughnut',
      data: data,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 20,
              font: {
                family: "'Poppins', sans-serif"
              }
            }
          }
        },
        cutout: '70%'
      }
    };
  
    // Cria o gráfico
    new Chart(ctx, config);
  }

  /*
  |--------------------------------------------------------------------------
  | Seção 3: Lógica do Modal de Transação (Formulário)
  |--------------------------------------------------------------------------
  */

  // --- Seleção de Elementos ---
  const modalOverlay = document.getElementById('modal-overlay');
  const modal = document.getElementById('transaction-modal');
  const modalForm = document.getElementById('modal-form');
  const modalTitle = document.getElementById('modal-title');
  const modalHeader = document.querySelector('.modal-header');
  const modalTypeInput = document.getElementById('modal-type');
  const modalConfirmBtn = document.getElementById('modal-confirm-btn');
  const closeModalBtn = document.getElementById('modal-close-btn');
  const cancelModalBtn = document.getElementById('modal-cancel-btn');

  // Botões de Gatilho (Desktop)
  const btnAddRenda = document.getElementById('btn-add-renda');
  const btnAddDespesa = document.getElementById('btn-add-despesa');
  const btnAddMeta = document.getElementById('btn-add-metas');

  // --- Funções ---

  /**
   * Abre o modal de transação e o personaliza.
   * @param {string} type - 'renda', 'despesa' ou 'meta'
   * @param {string} title - O título a ser exibido no modal
   * @param {string} headerClass - A classe CSS para colorir o cabeçalho
   */
  function openModal(type, title, headerClass) {
    if (!modal) return;

    // Personaliza o conteúdo
    modalTitle.textContent = title;
    modalTypeInput.value = type;
    
    // Reseta e aplica a classe de cor
    modalHeader.className = 'modal-header'; 
    modalHeader.classList.add(headerClass); 

    // Personaliza a cor do botão de confirmação
    if (type === 'renda') {
      modalConfirmBtn.style.backgroundColor = '#17b26a';
      modalConfirmBtn.style.borderColor = '#17b26a';
    } else if (type === 'despesa') {
      modalConfirmBtn.style.backgroundColor = '#f04438';
      modalConfirmBtn.style.borderColor = '#f04438';
    } else {
      modalConfirmBtn.style.backgroundColor = '#155eef';
      modalConfirmBtn.style.borderColor = '#155eef';
    }

    // Define a data atual como padrão
    document.getElementById('modal-data').valueAsDate = new Date();

    // Exibe o modal
    modalOverlay.classList.add('active');
    modal.classList.add('active');
  }

  /**
   * Fecha e reseta o modal de transação.
   */
  function closeModal() {
    if (!modal) return;
    modalOverlay.classList.remove('active');
    modal.classList.remove('active');
    if (modalForm) {
      modalForm.reset();
    }
  }

  // --- Event Listeners (Gatilhos de Abertura) ---
  if (btnAddRenda) {
    btnAddRenda.addEventListener('click', () => {
      openModal('renda', 'Adicionar Renda', 'modal-header-renda');
    });
  }
  if (btnAddDespesa) {
    btnAddDespesa.addEventListener('click', () => {
      openModal('despesa', 'Adicionar Despesa', 'modal-header-despesa');
    });
  }
  if (btnAddMeta) {
    btnAddMeta.addEventListener('click', () => {
      openModal('meta', 'Adicionar Valor à Meta', 'modal-header-metas');
    });
  }

  // --- Event Listeners (Gatilhos de Fechamento) ---
  if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
  if (cancelModalBtn) cancelModalBtn.addEventListener('click', closeModal);
  if (modalOverlay) {
    modalOverlay.addEventListener('click', (event) => {
      if (event.target === modalOverlay) closeModal();
    });
  }

  // --- Event Listener (Submissão do Formulário) ---
  if (modalForm) {
    modalForm.addEventListener('submit', (event) => {
      event.preventDefault(); // Impede o recarregamento da página
      
      const formData = new FormData(modalForm);
      const data = Object.fromEntries(formData.entries());
      
      console.log("Formulário de Transação Enviado:", data);
      
      // TODO: Enviar 'data' para o backend (ex: via fetch)
      
      closeModal();
    });
  }

  /*
  |--------------------------------------------------------------------------
  | Seção 4: Lógica do Modal de Escolha (Mobile)
  |--------------------------------------------------------------------------
  */

  // --- Seleção de Elementos ---
  const chooserOverlay = document.getElementById('chooser-overlay');
  const chooserModal = document.getElementById('action-chooser-modal');
  const btnAddUnified = document.getElementById('btn-add-unified'); // Botão flutuante
  const chooserCloseBtn = document.getElementById('chooser-close-btn');
  const chooserBtnRenda = document.getElementById('chooser-btn-renda');
  const chooserBtnDespesa = document.getElementById('chooser-btn-despesa');
  const chooserBtnMetas = document.getElementById('chooser-btn-metas');

  // --- Funções ---
  function openChooserModal() {
    if (!chooserModal) return;
    chooserOverlay.classList.add('active');
    chooserModal.classList.add('active');
  }

  function closeChooserModal() {
    if (!chooserModal) return;
    chooserOverlay.classList.remove('active');
    chooserModal.classList.remove('active');
  }

  // --- Event Listeners ---
  if (btnAddUnified) {
    btnAddUnified.addEventListener('click', openChooserModal);
  }
  if (chooserCloseBtn) {
    chooserCloseBtn.addEventListener('click', closeChooserModal);
  }
  if (chooserOverlay) {
    chooserOverlay.addEventListener('click', (event) => {
      if (event.target === chooserOverlay) closeChooserModal();
    });
  }

  // --- Listeners de Escolha (ligam um modal ao outro) ---
  if (chooserBtnRenda) {
    chooserBtnRenda.addEventListener('click', () => {
      closeChooserModal();
      setTimeout(() => {
        openModal('renda', 'Adicionar Renda', 'modal-header-renda');
      }, 300); // Atraso para animação
    });
  }

  if (chooserBtnDespesa) {
    chooserBtnDespesa.addEventListener('click', () => {
      closeChooserModal();
      setTimeout(() => {
        openModal('despesa', 'Adicionar Despesa', 'modal-header-despesa');
      }, 300);
    });
  }

  if (chooserBtnMetas) {
    chooserBtnMetas.addEventListener('click', () => {
      closeChooserModal();
      setTimeout(() => {
        openModal('meta', 'Adicionar Valor à Meta', 'modal-header-metas');
      }, 300);
    });
  }

}); // Fim do 'DOMContentLoaded'