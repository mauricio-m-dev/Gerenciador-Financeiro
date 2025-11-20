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
  | Seção 0: Leitura de Dados Iniciais
  |--------------------------------------------------------------------------
  */
  
  // Lê a "Data Island" uma vez e armazena os dados.
  let appData = {
    categorias: { renda: [], despesa: [] },
    chartData: { labels: ['Erro'], valores: [100] }
  };
  
  try {
    const appDataElement = document.getElementById('app-data');
    if (appDataElement) {
      appData = JSON.parse(appDataElement.textContent);
    } else {
      console.error("Elemento #app-data não encontrado.");
    }
  } catch (e) {
    console.error("Erro ao ler dados JSON do #app-data:", e);
  }

  /*
  |--------------------------------------------------------------------------
  | Seção 1: Navbar e Interações do Header (AQUI ESTÁ A CORREÇÃO)
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
    // CORREÇÃO: Pega os dados lidos da "Data Island"
    const chartLabels = appData.chartData.labels;
    const chartValores = appData.chartData.valores;
  
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
  const modalCategoriaSelect = document.getElementById('modal-categoria');
  const modalDataInput = document.getElementById('modal-data');

  // Botões de Gatilho (Desktop)
  const btnAddRenda = document.getElementById('btn-add-renda');
  const btnAddDespesa = document.getElementById('btn-add-despesa');
  const btnAddMeta = document.getElementById('btn-add-metas');

  // --- Funções ---

  /**
   * NOVO: Formata a data atual para o input datetime-local (YYYY-MM-DDTHH:MM)
   */
  function getDatetimeLocalNow() {
    const now = new Date();
    // Ajusta para o fuso horário local
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    return now.toISOString().slice(0, 16);
  }
  
  /**
   * NOVO: Preenche o select de categorias
   */
  function populateCategorias(tipo) {
    if (!modalCategoriaSelect) return;
    
    modalCategoriaSelect.innerHTML = ''; // Limpa opções antigas
    
    const categorias = (tipo === 'renda') ? appData.categorias.renda : appData.categorias.despesa;
    
    if (!categorias || categorias.length === 0) {
      modalCategoriaSelect.innerHTML = `<option value="" disabled selected>Nenhuma categoria de ${tipo} encontrada</option>`;
      return;
    }
  
    modalCategoriaSelect.innerHTML = `<option value="" disabled selected>Selecione uma categoria</option>`;
    
    categorias.forEach(cat => {
      const option = document.createElement('option');
      option.value = cat.id;
      option.textContent = cat.nome;
      modalCategoriaSelect.appendChild(option);
    });
  }

  /**
    * Abre o modal de transação e o personaliza.
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

    // NOVO: Define a data atual como padrão
    if (modalDataInput) {
      modalDataInput.value = getDatetimeLocalNow();
    }
    
    // NOVO: Preenche as categorias
    populateCategorias(type);

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
    // Reseta o select de categoria
    if (modalCategoriaSelect) {
        modalCategoriaSelect.innerHTML = '<option value="" disabled selected>Selecione o tipo primeiro</option>';
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
      // A lógica 'meta' deve ser tratada aqui
      // Por enquanto, vamos assumir que é 'renda' para o formulário
      openModal('renda', 'Adicionar Valor à Meta', 'modal-header-metas');
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
      console.log("Formulário enviado. A página será recarregada.");
      // O event.preventDefault() foi removido para deixar o formulário enviar
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
        openModal('renda', 'Adicionar Valor à Meta', 'modal-header-metas');
      }, 300);
    });
  }

  

}); // Fim do 'DOMContentLoaded'