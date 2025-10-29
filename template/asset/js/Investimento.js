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

  // Modal
  const exampleModal = document.getElementById('exampleModal')
  if (exampleModal) {
    exampleModal.addEventListener('show.bs.modal', event => {
      // Button that triggered the modal
      const button = event.relatedTarget
      // Extract info from data-bs-* attributes
      const recipient = button.getAttribute('data-bs-whatever')
      // If necessary, you could initiate an Ajax request here
      // and then do the updating in a callback.

      // Update the modal's content.
      const modalTitle = exampleModal.querySelector('.modal-title')
      const modalBodyInput = exampleModal.querySelector('.modal-body input')

      modalTitle.textContent = `New message to ${recipient}`
      modalBodyInput.value = recipient
    })
  }


  //Gráfico
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


  /* ---------- Autocomplete de ações (mock, trocar por API depois) ---------- */
  const stockInput = document.getElementById('stock-search');
  const suggestionsEl = document.getElementById('stock-suggestions');
  const hiddenSymbol = document.getElementById('stock-symbol');

  // Mock de exemplo — substitua por fetch para API real
  const mockStocks = [
    { symbol: 'PETR4', name: 'Petrobras PN' },
    { symbol: 'VALE3', name: 'Vale ON' },
    { symbol: 'ITUB4', name: 'Itaú Unibanco PN' },
    { symbol: 'ABEV3', name: 'Ambev ON' },
    { symbol: 'BBDC4', name: 'Bradesco PN' },
    { symbol: 'MGLU3', name: 'Magazine Luiza ON' },
    { symbol: 'APPL34', name: 'Apple Inc.' },
    { symbol: 'TSLA34', name: 'Tesla Inc.' }
  ];

  let stocks = [
    'PETR4',
    'VALE3',
    'ITUB4',
    'ABEV3',
    'BBDC4',
    'MGLU3',
    'APPL34',
    'TSLA34',
  ]

  let debounceTimer = null;
  let activeIndex = -1;
  let currentList = [];

  function openList() {
    suggestionsEl.style.display = 'block';
    stockInput.setAttribute('aria-expanded', 'true');
  }
  function closeList() {
    suggestionsEl.style.display = 'none';
    stockInput.setAttribute('aria-expanded', 'false');
    activeIndex = -1;
  }

  function renderList(list) {
    suggestionsEl.innerHTML = '';
    currentList = list;
    if (!list.length) { closeList(); return; }
    list.forEach((item, i) => {
      const el = document.createElement('div');
      el.className = 'autocomplete-item';
      el.setAttribute('role', 'option');
      el.setAttribute('data-index', i);
      el.innerHTML = `<span class="autocomplete-symbol">${item.symbol}</span>
                        <span class="autocomplete-name">${item.name}</span>`;
      el.addEventListener('click', () => selectItem(i));
      suggestionsEl.appendChild(el);
    });
    openList();
  }

  function selectItem(index) {
    const item = currentList[index];
    if (!item) return;
    stockInput.value = `${item.symbol} — ${item.name}`;
    hiddenSymbol.value = item.symbol;
    closeList();
  }

  function filterStocks(query) {
    if (!query) return [];
    const q = query.trim().toLowerCase();
    // procura por símbolo ou nome
    return mockStocks.filter(s =>
      s.symbol.toLowerCase().includes(q) || s.name.toLowerCase().includes(q)
    ).slice(0, 8); // limite
  }

  stockInput.addEventListener('input', (e) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      const q = e.target.value;
      // se for API, aqui chamaria fetch(...) e depois renderList(...)
      const results = filterStocks(q);
      renderList(results);
    }, 250);
  });

  stockInput.addEventListener('keydown', (e) => {
    const items = suggestionsEl.querySelectorAll('.autocomplete-item');
    if (suggestionsEl.style.display === 'none' || items.length === 0) return;
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      activeIndex = Math.min(activeIndex + 1, items.length - 1);
      updateActive(items);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      activeIndex = Math.max(activeIndex - 1, 0);
      updateActive(items);
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (activeIndex >= 0) selectItem(activeIndex);
    } else if (e.key === 'Escape') {
      closeList();
    }
  });

  function updateActive(items) {
    items.forEach((it, idx) => {
      it.setAttribute('aria-selected', idx === activeIndex ? 'true' : 'false');
      if (idx === activeIndex) it.scrollIntoView({ block: 'nearest' });
    });
  }

  // Fecha ao clicar fora
  document.addEventListener('click', (e) => {
    if (!suggestionsEl.contains(e.target) && e.target !== stockInput) closeList();
  });

  // Se o modal for fechado via bootstrap, limpa estado
  const modalEl = document.getElementById('staticBackdrop');
  if (modalEl) {
    modalEl.addEventListener('hidden.bs.modal', () => {
      stockInput.value = '';
      hiddenSymbol.value = '';
      closeList();
    });
  }

  //Autocomplete da div Mercado
  const resultsbox = document.querySelector(".result-box");
  const pesquisa = document.getElementById("pesquisa-mercado");

  pesquisa.onkeyup = function () {
    let resultado = [];
    let input = pesquisa.value;

    if (input.length) {
      resultado = stocks.filter((keyword) => {
        return keyword.toLowerCase().includes(input.toLowerCase());
      });
      console.log(resultado)
      
      display(resultado)
    }
  }

  function display(resultado){
    const content = resultado.map((list)=>{
      return "<li onclick=selectInput(this)>" + list + "</li>";
    });

    resultsbox.innerHTML = "<ul>" + content.join('') + "</ul>";
  }

  function selectInput(list){
    pesquisa.value = list.innerHTML;
  }





});