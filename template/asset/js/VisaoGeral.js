/*
|--------------------------------------------------------------------------
| VisaoGeral.js (Versão Corrigida sem Conflitos)
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", () => {

  /*
  |--------------------------------------------------------------------------
  | Seção 0: Leitura de Dados Iniciais
  |--------------------------------------------------------------------------
  */
  let appData = {
    categorias: { renda: [], despesa: [] },
    chartData: { labels: ['Erro'], valores: [100] }
  };
  
  try {
    const appDataElement = document.getElementById('app-data');
    if (appDataElement) {
      appData = JSON.parse(appDataElement.textContent);
    }
  } catch (e) {
    console.error("Erro ao ler dados JSON:", e);
  }

  /*
  |--------------------------------------------------------------------------
  | Seção 1: Navbar e Header
  |--------------------------------------------------------------------------
  */
  const hamburger = document.querySelector(".hamburger");
  const navLinks = document.querySelector(".nav-links");

  if (hamburger && navLinks) {
    hamburger.addEventListener("click", () => {
      hamburger.classList.toggle("active");
      navLinks.classList.toggle("active");
    });
    document.querySelectorAll(".nav-links li a").forEach((link) => {
      link.addEventListener("click", () => {
        if (navLinks.classList.contains("active")) {
          hamburger.classList.remove("active");
          navLinks.classList.remove("active");
        }
      });
    });
  }

  const header = document.querySelector("header");
  if (header) {
    window.addEventListener("scroll", () => {
      if (window.scrollY > 20) header.classList.add("scrolled");
      else header.classList.remove("scrolled");
    });
  }

  /*
  |--------------------------------------------------------------------------
  | Seção 2: Gráfico (Chart.js)
  |--------------------------------------------------------------------------
  */
  const ctx = document.getElementById('expenseDoughnutChart');
  if (ctx) {
    const chartLabels = appData.chartData.labels;
    const chartValores = appData.chartData.valores;
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: chartLabels,
        datasets: [{
          label: 'Despesas (R$)',
          data: chartValores,
          backgroundColor: ['#F56565', '#155EEF', '#48BB78', '#ED8936', '#718096'],
          borderColor: '#ffffff',
          borderWidth: 3,
          hoverOffset: 10
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } },
        cutout: '70%'
      }
    });
  }

  /*
  |--------------------------------------------------------------------------
  | Seção 3: Modal de Transação (Apenas Renda e Despesa)
  |--------------------------------------------------------------------------
  */
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

  const btnAddRenda = document.getElementById('btn-add-renda');
  const btnAddDespesa = document.getElementById('btn-add-despesa');
  
  // CORREÇÃO: Removi btnAddMeta daqui para não abrir o modal errado

  function getDatetimeLocalNow() {
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    return now.toISOString().slice(0, 16);
  }
  
  function populateCategorias(tipo) {
    if (!modalCategoriaSelect) return;
    modalCategoriaSelect.innerHTML = ''; 
    const categorias = (tipo === 'renda') ? appData.categorias.renda : appData.categorias.despesa;
    if (!categorias || categorias.length === 0) {
      modalCategoriaSelect.innerHTML = `<option value="" disabled selected>Nenhuma categoria encontrada</option>`;
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

  function openModal(type, title, headerClass) {
    if (!modal) return;
    modalTitle.textContent = title;
    modalTypeInput.value = type;
    modalHeader.className = 'modal-header ' + headerClass; 

    if (type === 'renda') {
      modalConfirmBtn.style.backgroundColor = '#17b26a';
      modalConfirmBtn.style.borderColor = '#17b26a';
    } else {
      modalConfirmBtn.style.backgroundColor = '#f04438';
      modalConfirmBtn.style.borderColor = '#f04438';
    }

    if (modalDataInput) modalDataInput.value = getDatetimeLocalNow();
    populateCategorias(type);

    modalOverlay.classList.add('active');
    modal.classList.add('active');
  }

  function closeModal() {
    if (!modal) return;
    modalOverlay.classList.remove('active');
    modal.classList.remove('active');
    if (modalForm) modalForm.reset();
  }

  // Listeners para Renda e Despesa
  if (btnAddRenda) btnAddRenda.addEventListener('click', () => openModal('renda', 'Adicionar Renda', 'modal-header-renda'));
  if (btnAddDespesa) btnAddDespesa.addEventListener('click', () => openModal('despesa', 'Adicionar Despesa', 'modal-header-despesa'));
  
  // CORREÇÃO: O listener do botão de meta foi removido daqui para ser tratado exclusivamente na Seção 5

  if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
  if (cancelModalBtn) cancelModalBtn.addEventListener('click', closeModal);
  if (modalOverlay) {
    modalOverlay.addEventListener('click', (event) => {
      if (event.target === modalOverlay) closeModal();
    });
  }

  /*
  |--------------------------------------------------------------------------
  | Seção 4: Lógica do Modal de Escolha (Mobile)
  |--------------------------------------------------------------------------
  */
  const chooserOverlay = document.getElementById('chooser-overlay');
  const chooserModal = document.getElementById('action-chooser-modal');
  const btnAddUnified = document.getElementById('btn-add-unified');
  const chooserCloseBtn = document.getElementById('chooser-close-btn');
  const chooserBtnRenda = document.getElementById('chooser-btn-renda');
  const chooserBtnDespesa = document.getElementById('chooser-btn-despesa');
  const chooserBtnMetas = document.getElementById('chooser-btn-metas');

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

  if (btnAddUnified) btnAddUnified.addEventListener('click', openChooserModal);
  if (chooserCloseBtn) chooserCloseBtn.addEventListener('click', closeChooserModal);
  if (chooserOverlay) {
    chooserOverlay.addEventListener('click', (event) => {
      if (event.target === chooserOverlay) closeChooserModal();
    });
  }

  if (chooserBtnRenda) {
    chooserBtnRenda.addEventListener('click', () => {
      closeChooserModal();
      setTimeout(() => openModal('renda', 'Adicionar Renda', 'modal-header-renda'), 300);
    });
  }

  if (chooserBtnDespesa) {
    chooserBtnDespesa.addEventListener('click', () => {
      closeChooserModal();
      setTimeout(() => openModal('despesa', 'Adicionar Despesa', 'modal-header-despesa'), 300);
    });
  }

  // CORREÇÃO: Removi o listener do chooserBtnMetas daqui para não conflitar com a Seção 5

  /*
  |--------------------------------------------------------------------------
  | Seção 5: NOVA LÓGICA DE METAS (Corrigida e Exclusiva)
  |--------------------------------------------------------------------------
  */
  const btnAddMetaDesktop = document.getElementById('btn-add-metas');
  const modalMeta = document.getElementById('modal-meta-aporte');
  const closeMetaBtn = document.getElementById('close-meta-modal');
  const cancelMetaBtn = document.getElementById('cancel-meta-btn');
  const formMeta = document.getElementById('form-meta-aporte');

  // Função para abrir o modal ESPECÍFICO de metas
  function openMetaModal() {
    if (!modalMeta) {
        console.error("Modal de meta não encontrado no DOM");
        return;
    }
    
    // Se veio do mobile, fecha o chooser antes
    closeChooserModal();
    
    // Usa o mesmo overlay principal
    if (modalOverlay) modalOverlay.classList.add('active');
    
    modalMeta.style.display = 'block';
    setTimeout(() => modalMeta.classList.add('active'), 10);
  }

  // Função para fechar o modal de metas
  function closeMetaModal() {
    if (!modalMeta) return;
    modalMeta.classList.remove('active');
    if (modalOverlay) modalOverlay.classList.remove('active');
    
    setTimeout(() => {
        modalMeta.style.display = 'none';
        if(formMeta) formMeta.reset();
    }, 300);
  }

  // 1. Botão Desktop
  if (btnAddMetaDesktop) {
      // Removemos listeners antigos clonando o nó (hack rápido) ou apenas garantindo que este código rode limpo
      // Como removemos o código conflitante acima, apenas adicionar o listener resolve:
      btnAddMetaDesktop.addEventListener('click', (e) => {
          e.preventDefault(); // Previne comportamento padrão
          openMetaModal();
      });
  }
  
  // 2. Botão Mobile (Chooser)
  if (chooserBtnMetas) {
    chooserBtnMetas.addEventListener('click', (e) => {
        e.preventDefault();
        openMetaModal(); 
    });
  }

  // Listeners de Fechamento
  if (closeMetaBtn) closeMetaBtn.addEventListener('click', closeMetaModal);
  if (cancelMetaBtn) cancelMetaBtn.addEventListener('click', closeMetaModal);
  
  // Fecha ao clicar fora (Overlay)
  if (modalOverlay) {
      modalOverlay.addEventListener('click', (e) => {
          // Só fecha se o modal de meta estiver visível (style.display block)
          if (modalMeta && modalMeta.style.display === 'block') {
              closeMetaModal();
          }
      });
  }

  // --- ENVIO DO FORMULÁRIO (AJAX) ---
  if (formMeta) {
    formMeta.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = formMeta.querySelector('button[type="submit"]');
        const originalText = btn.textContent;
        btn.textContent = 'Enviando...';
        btn.disabled = true;

        const formData = new FormData(formMeta);
        const data = Object.fromEntries(formData.entries());
        data.amount = parseFloat(data.amount);

        try {
            // Ajuste o caminho se necessário (ex: '../metas.php' ou '../../metas.php')
            const response = await fetch('../metas.php?action=contribute', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            // Tenta ler como texto primeiro para debug de erro HTML
            const textResponse = await response.text();
            let result;
            try {
                result = JSON.parse(textResponse);
            } catch(err) {
                console.error("Erro JSON:", textResponse);
                alert("Erro inesperado do servidor. Verifique o console.");
                return;
            }

            if (result.status === 'success') {
                alert('Sucesso: ' + result.message);
                closeMetaModal();
                location.reload(); 
            } else {
                alert('Erro: ' + result.message);
            }

        } catch (error) {
            console.error(error);
            alert('Erro de comunicação com o servidor.');
        } finally {
            btn.textContent = originalText;
            btn.disabled = false;
        }
    });
  }

}); // Fim do DOMContentLoaded