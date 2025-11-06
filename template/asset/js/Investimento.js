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

  // Processo para chamar as informações da API

  const URL_LISTA = `https://brapi.dev/api/quote/list?type=stock&limit=1000`;
  const TOKEN_BRAPI = 'pz4ZsbSAgwP96rCKvp52Pq';

  const config_token = {
    headers: {
      'Authorization': `Bearer ${TOKEN_BRAPI}` // ESSENCIAL!
    }
  }

  let objFiltro = []

  async function chamarAPI(params) {
    const resp = await fetch(URL_LISTA, config_token);
    if (resp.status === 200) {
      const obj = await resp.json();
      objFiltro = obj.stocks.map(acao => {

        return {
          ticket: acao.stock,
          nome: acao.name,
          valor: acao.close,

        };
      }
      );

      console.log(objFiltro);
    }
  }

  chamarAPI()


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

  let resultado = [];

  // 1. Listener de Input (lógica 100% do 2º script)
  stockInput.addEventListener("input", function () {
    let input = stockInput.value; // Pega o valor do input 1
    let  q = input.trim().toLowerCase()

    if (input.length) {
      // lógica de filtragem (do 2º)
      resultado = objFiltro.filter(item => {
        // Procura no ticker OU no nome
        return item.ticket.toLowerCase().includes(q) ||
          item.nome.toLowerCase().includes(q);
      }).slice(0, 8); // Limita a 8 resultados

      displaySuggestions(resultado);
    }

    else {
    // Se o input estiver vazio (do 2º)
    suggestionsEl.innerHTML = "";
    suggestionsEl.style.display = 'none'; // Adicionado para garantir que feche
  }
});

// 2. Função de Renderização (lógica 100% do 'display' do 2º script)
function displaySuggestions(resultado) {
  if (!resultado.length) {
    suggestionsEl.innerHTML = "";
    suggestionsEl.style.display = 'none';
    return;
  }

  const content = resultado.map((item, index) => { // Mudei 'list' para 'item' para clareza
        // Use a formatação do objeto (ticker e nome)
        // Adicionei o data-index para o clique funcionar corretamente, como discutimos antes.
        return `<li data-index="${index}"><strong>${item.ticket}</strong> - ${item.nome}</li>`; 
    }).join('');

  // Coloca dentro de um <ul>, como o 2º script
  suggestionsEl.innerHTML = "<ul>" + content + "</ul>";
  suggestionsEl.style.display = 'block'; // Mostra o container
}

// 3. Função de Seleção (lógica 100% do 'selectInput' do 2º script)
function selectSuggestion(listItem) {
    const index = parseInt(listItem.getAttribute('data-index'), 10);
    const item = resultado[index]; 

    if (!item) return;

    // CORRIGIDO: Agora usa consistentemente 'item.ticker'
    stockInput.value = `${item.ticket} — ${item.nome}`; 
    hiddenSymbol.value = item.ticket; 

    suggestionsEl.innerHTML = "";
    suggestionsEl.style.display = 'none';
}

// 4. Listener de Clique (lógica 100% do 2º)
suggestionsEl.addEventListener('click', function (event) {
  // Exatamente a lógica do 2º
  if (event.target.tagName == "LI") {
    selectSuggestion(event.target);
  }
});

// 5. Bônus: Fechar ao clicar fora (o 2º não tem, mas é vital)
document.addEventListener('click', (e) => {
  if (!suggestionsEl.contains(e.target) && e.target !== stockInput) {
    suggestionsEl.innerHTML = "";
    suggestionsEl.style.display = 'none';
  }
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



// Autocomplete da div Mercado
const resultsbox = document.querySelector(".result-box");
const pesquisa = document.getElementById("pesquisa-mercado");

// Seleciona o item das sugestões 
resultsbox.addEventListener('click', function (event) {
  if (event.target.tagName == "LI") {
    selectInput(event.target);
  }
})

pesquisa.addEventListener("input", function () {
  let resultado = [];
  let input = pesquisa.value;

  if (input.length) {
    // lógica de filtragem 
    resultado = stocks.filter((keyword) => {
      return keyword.toLowerCase().includes(input.toLowerCase());
    });
    console.log(resultado);
    display(resultado);
  } else {
    // Se o input estiver vazio (clique no 'x' ou apagar com backspace), limpa a caixa de resultados
    resultsbox.innerHTML = "";
  }
});

function display(resultado) {
  const content = resultado.map((list) => {
    return "<li>" + list + "</li>";
  });

  resultsbox.innerHTML = "<ul>" + content.join('') + "</ul>";
}

function selectInput(list) {
  pesquisa.value = list.innerHTML;
  resultsbox.innerHTML = "";
}

});