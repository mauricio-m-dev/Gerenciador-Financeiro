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

  //Inputs de valor do modal
  const valorTotalInput = document.getElementById('investment-value-total');
  const investmentUnit = document.getElementById('investment-value-unit');
  const qtdInput = document.getElementById('qtd-input');
  


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
    let q = input.trim().toLowerCase()

    if (input.length) {
      // lógica de filtragem (do 2º)
      resultado = objFiltro.filter(item => {
        // Procura no ticker OU no nome
        return item.ticket.toLowerCase().includes(q) ||
          item.nome.toLowerCase().includes(q);
          item.valor.toString().toLowerCase().includes(q);
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
    document.getElementById('stock-name').value = item.nome;

    // Preenche o valor unitário no modal
    const investmentUnit = document.getElementById('investment-value-unit');
    if (investmentUnit) {
      investmentUnit.value = item.valor;
    }

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
       investmentUnit.value = '';
      valorTotalInput.value = '';
      qtdInput.value = '1';
      suggestionsEl.innerHTML = "";
      suggestionsEl.style.display = 'none';
    });
  }

  // --- 7. Salvar Investimento (Integração com API) ---
  const btnSalvarInvestimento = document.querySelector('.modal-footer .btn-primary');
  if (btnSalvarInvestimento) {
    btnSalvarInvestimento.addEventListener('click', async (e) => {
      e.preventDefault();

      const assetSymbol = hiddenSymbol.value.trim();
      const quantidade = parseInt(qtdInput.value, 10);
      const valorUnitario = parseFloat(investmentUnit.value.replace(',', '.'));

      if (!assetSymbol) {
        alert('Por favor, selecione uma ação');
        return;
      }

      if (!quantidade || quantidade <= 0) {
        alert('Quantidade deve ser maior que 0');
        return;
      }

      if (!valorUnitario || valorUnitario <= 0) {
        alert('Valor unitário deve ser maior que 0');
        return;
      }

      try {
        const assetName = document.getElementById('stock-name').value;
        
        const resposta = await fetch('./api_investimento.php?acao=comprar', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            asset_symbol: assetSymbol,
            asset_name: assetName,
            quantidade: quantidade,
            valor_unitario: valorUnitario
          })
        });

        const resultado = await resposta.json();

        if (resultado.sucesso) {
          alert(resultado.mensagem);
          
          // Fecha o modal
          const modal = bootstrap.Modal.getInstance(modalEl);
          modal.hide();

          // Limpa o formulário
          stockInput.value = '';
          hiddenSymbol.value = '';
          document.getElementById('stock-name').value = '';
          investmentUnit.value = '';
          valorTotalInput.value = '';
          qtdInput.value = '1';

          // Recarrega a carteira (você pode implementar uma função para isso)
          carregarCarteira();
        } else {
          alert('Erro: ' + resultado.mensagem);
        }
      } catch (erro) {
        console.error('Erro ao salvar investimento:', erro);
        alert('Erro ao processar a requisição');
      }
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

  // --- 6. Controles de quantidade (+ / -) no modal de adicionar investimento ---
  

  
  // Ao perder foco, garante que o valor seja um inteiro >= 1
  if (qtdInput) {
    qtdInput.addEventListener('blur', (e) => {
      let v = parseInt(e.target.value, 10);
      if (isNaN(v) || v < 1) v = 1;
      e.target.value = v;
    });
  }

  //Fazer o campo de valor total ser atualizado conforme a quantidade e o valor for alterada
  qtdInput.addEventListener('input', (e) => {
    
    let numerototal = investmentUnit.value * qtdInput.value;
    valorTotalInput.value = Math.round(numerototal * 100) / 100;
  })

  investmentUnit.addEventListener('input', (e) => {
    
    let numerototal = investmentUnit.value * qtdInput.value;
    valorTotalInput.value = Math.round(numerototal * 100) / 100;
  })

  // --- 8. Carregar Carteira do Banco de Dados ---
  async function carregarCarteira() {
    try {
      const resposta = await fetch('./api_investimento.php?acao=carteira');
      const resultado = await resposta.json();

      if (resultado.sucesso) {
        atualizarTabelaCotas(resultado.carteira);
      }
    } catch (erro) {
      console.error('Erro ao carregar carteira:', erro);
    }
  }

  // Atualiza a tabela de "Seus ativos" com dados do banco
  function atualizarTabelaCotas(carteira) {
    const tbody = document.querySelector('.transactions-table tbody');
    
    if (!tbody) return;

    if (carteira.length === 0) {
      tbody.innerHTML = '<tr><td colspan="2" class="empty-table-message">Nenhum ativo na carteira.</td></tr>';
      return;
    }

    tbody.innerHTML = carteira.map(ativo => `
      <tr>
        <td class="transaction-name">${ativo.asset_name} (${ativo.asset_symbol})</td>
        <td class="amount-income align-right">${ativo.total_cotas}</td>
      </tr>
    `).join('');
  }

  // Carrega a carteira ao inicializar a página
  carregarCarteira();
});