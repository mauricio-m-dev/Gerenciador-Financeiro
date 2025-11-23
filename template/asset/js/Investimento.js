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


  //Gráfico - Variável global para armazenar a instância do Chart
  let graficoInstancia = null;

  function inicializarGrafico(labels, valores) {
    console.log("inicializarGrafico chamada com:", labels, valores);
    
    // Busca o elemento do canvas
    const canvasElement = document.getElementById('expenseDoughnutChart');
    
    if (!canvasElement) {
      console.error("Elemento canvas não encontrado!");
      return;
    }

    console.log("Canvas encontrado, obtendo contexto...");
    
    // Destroi gráfico anterior se existir
    if (graficoInstancia) {
      console.log("Destruindo gráfico anterior...");
      graficoInstancia.destroy();
    }

    // Valida dados
    if (!labels || labels.length === 0 || !valores || valores.length === 0) {
      console.warn("Dados vazios para o gráfico");
      return;
    }

    // Cores para o gráfico
    const cores = [
      '#F56565', '#155EEF', '#48BB78', '#ED8936', '#718096',
      '#38B6FF', '#FF6B9D', '#FDB833', '#33658A', '#86C06F',
      '#F18F01', '#C73E1D', '#6A994E', '#BC4749', '#2E294E'
    ];

    const coresAssinadas = labels.map((_, index) => cores[index % cores.length]);

    console.log("Criando configuração do gráfico...");

    // Configuração do gráfico
    const chartConfig = {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          label: 'Valor Investido (R$)',
          data: valores,
          backgroundColor: coresAssinadas,
          borderColor: '#ffffff',
          borderWidth: 3,
          hoverOffset: 10
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 20,
              font: {
                family: "'Poppins', sans-serif",
                size: 12
              }
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                const value = context.parsed;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((value / total) * 100).toFixed(1);
                return `${context.label}: R$ ${value.toFixed(2)} (${percentage}%)`;
              }
            }
          }
        },
        cutout: '70%'
      }
    };

    try {
      console.log("Tentando criar novo Chart (modo simples)...");
      // Cria o gráfico usando o contexto 2D (simples e direto)
      try {
        const ctx2d = canvasElement.getContext('2d');
        graficoInstancia = new Chart(ctx2d, chartConfig);
        console.log("✓ Gráfico criado com sucesso!");
      } catch (erroChart) {
        console.error('Erro ao criar Chart (simples):', erroChart);
        throw erroChart;
      }
    } catch (erro) {
      console.error("✗ Erro ao criar o gráfico:", erro);
      console.error("Detalhes do erro:", erro && erro.message ? erro.message : erro);

      // Fallback visual: desenha um arco simples para indicar presença de dados
      try {
        const ctxfb = canvasElement.getContext('2d');
        ctxfb.clearRect(0, 0, canvasElement.width, canvasElement.height);
        ctxfb.fillStyle = '#e9ecef';
        ctxfb.fillRect(0, 0, canvasElement.width, canvasElement.height);
        ctxfb.beginPath();
        const cx = canvasElement.width / 2;
        const cy = canvasElement.height / 2;
        const radius = Math.min(cx, cy) * 0.6;
        ctxfb.fillStyle = '#6c757d';
        ctxfb.arc(cx, cy, radius, 0, Math.PI * 2 * 0.75);
        ctxfb.fill();
        ctxfb.closePath();
        console.log('Fallback desenhado no canvas');
      } catch (fbErr) {
        console.error('Falha ao desenhar fallback no canvas:', fbErr);
      }
    }
  }

  // Tenta carregar dados iniciais do canvas se existirem
  const ctxInicial = document.getElementById('expenseDoughnutChart');
  if (ctxInicial) {
    console.log("Canvas encontrado!");
    console.log("data-labels:", ctxInicial.dataset.labels);
    console.log("data-valores:", ctxInicial.dataset.valores);
    
    if (ctxInicial.dataset.labels && ctxInicial.dataset.valores) {
      try {
        const chartLabels = JSON.parse(ctxInicial.dataset.labels);
        const chartValores = JSON.parse(ctxInicial.dataset.valores);
        
        console.log("Labels parseados:", chartLabels);
        console.log("Valores parseados:", chartValores);
        
        if (chartLabels.length > 0) {
          // Inicializa diretamente (modo simples)
          inicializarGrafico(chartLabels, chartValores);
        }
      } catch (e) {
        console.error("Erro ao fazer parse dos dados:", e);
      }
    } else {
      console.warn("Atributos data-labels ou data-valores não encontrados");
    }
  } else {
    console.error("Canvas expenseDoughnutChart não encontrado no DOM");
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
      // Após popular objFiltro, renderiza os cards do mercado
      try {
        renderMarketCards();
      } catch (e) {
        console.warn('Erro ao renderizar market cards:', e);
      }
      return objFiltro;
    }
    return [];
  }

  // Chama a API e renderiza os cards
  chamarAPI();

  /**
   * Renderiza até 10 cards do mercado dentro de #market-cards
   */
  function renderMarketCards() {
    const container = document.getElementById('market-cards');
    if (!container) return;

    const lista = objFiltro && objFiltro.length ? objFiltro.slice(0, 10) : [];
    if (!lista.length) {
      container.innerHTML = '<p class="text-muted">Não foi possível carregar as ações do mercado.</p>';
      return;
    }

    const cardsHtml = lista.map((item) => {
      const valorNum = typeof item.valor === 'number' ? item.valor : parseFloat(item.valor || 0);
      const valorFmt = (isNaN(valorNum) ? 0 : valorNum).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      return `
        <div class="card market-card">
          <div class="card-body">
            <h5 class="card-title">${item.ticket}</h5>
            <h6 class="card-subtitle mb-2 text-body-secondary">${item.nome}</h6>
            <p class="card-text">R$ ${valorFmt}</p>
          </div>
        </div>
      `;
    }).join('');

    // Insere os cards diretamente no container .acoes para respeitar o grid CSS
    container.innerHTML = cardsHtml;
  }


  /* ---------- Autocomplete de ações (mock, trocar por API depois) ---------- */
  const stockInput = document.getElementById('stock-search');
  const suggestionsEl = document.getElementById('stock-suggestions');
  const hiddenSymbol = document.getElementById('stock-symbol');

  //Inputs de valor do modal
  const valorTotalInput = document.getElementById('investment-value-total');
  const investmentUnit = document.getElementById('investment-value-unit');
  const qtdInput = document.getElementById('qtd-input');
  
  // Helper seguro para adicionar listeners apenas se o elemento existir
  function on(el, evt, handler, opts) {
    if (!el) return;
    el.addEventListener(evt, handler, opts);
  }


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
  on(stockInput, 'input', function () {
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
      if (suggestionsEl) {
        suggestionsEl.innerHTML = "";
        suggestionsEl.style.display = 'none'; // Adicionado para garantir que feche
      }
    }
  });

  // 2. Função de Renderização (lógica 100% do 'display' do 2º script)
  function displaySuggestions(resultado) {
    if (!resultado.length) {
      if (suggestionsEl) {
        suggestionsEl.innerHTML = "";
        suggestionsEl.style.display = 'none';
      }
      return;
    }

    const content = resultado.map((item, index) => { // Mudei 'list' para 'item' para clareza
      // Use a formatação do objeto (ticker e nome)
      // Adicionei o data-index para o clique funcionar corretamente, como discutimos antes.
      return `<li data-index="${index}"><strong>${item.ticket}</strong> - ${item.nome}</li>`;
    }).join('');

    // Coloca dentro de um <ul>, como o 2º script
    if (suggestionsEl) {
      suggestionsEl.innerHTML = "<ul>" + content + "</ul>";
      suggestionsEl.style.display = 'block'; // Mostra o container
    }
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
    if (investmentUnit) {
      investmentUnit.value = item.valor;
    }

    if (suggestionsEl) {
      suggestionsEl.innerHTML = "";
      suggestionsEl.style.display = 'none';
    }
  }

  // 4. Listener de Clique (lógica 100% do 2º)
  on(suggestionsEl, 'click', function (event) {
    // Exatamente a lógica do 2º
    if (event.target.tagName == "LI") {
      selectSuggestion(event.target);
    }
  });

  // 5. Bônus: Fechar ao clicar fora (o 2º não tem, mas é vital)
  document.addEventListener('click', (e) => {
    if (!suggestionsEl) return;
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
      if (suggestionsEl) {
        suggestionsEl.innerHTML = "";
        suggestionsEl.style.display = 'none';
      }
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
  on(resultsbox, 'click', function (event) {
    if (event.target.tagName == "LI") {
      selectInput(event.target);
    }
  })

  on(pesquisa, 'input', function () {
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
      if (resultsbox) resultsbox.innerHTML = "";
    }
  });

  function display(resultado) {
    const content = resultado.map((list) => {
      return "<li>" + list + "</li>";
    });

    if (resultsbox) resultsbox.innerHTML = "<ul>" + content.join('') + "</ul>";
  }

  function selectInput(list) {
    pesquisa.value = list.innerHTML;
    if (resultsbox) resultsbox.innerHTML = "";
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

  // Atualiza o campo de valor total conforme a quantidade e o valor unitário
  function atualizarTotalModal() {
    const unitRaw = investmentUnit ? investmentUnit.value : '0';
    const qtdRaw = qtdInput ? qtdInput.value : '0';

    const unit = parseFloat(String(unitRaw).replace(',', '.')) || 0;
    const qtd = parseFloat(String(qtdRaw).replace(',', '.')) || 0;

    const total = Math.round(unit * qtd * 100) / 100;

    if (valorTotalInput) {
      console.debug('atualizarTotalModal', { unit, qtd, total });
      valorTotalInput.value = total.toFixed(2);
    }
  }

  on(qtdInput, 'input', atualizarTotalModal);
  on(investmentUnit, 'input', atualizarTotalModal);

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
  async function atualizarTabelaCotas(carteira) {
    const tbody = document.querySelector('.transactions-table tbody');
    
    if (!tbody) return;

    if (carteira.length === 0) {
      tbody.innerHTML = '<tr><td colspan="3" class="empty-table-message">Nenhum ativo na carteira.</td></tr>';
      return;
    }

    // Carrega o histórico para obter os IDs das transações
    const historicoResp = await fetch('./api_investimento.php?acao=historico');
    const historicoData = await historicoResp.json();
    const historico = historicoData.sucesso ? historicoData.transacoes : [];

    // Cria um mapa de transações por ativo
    const mapaTransacoes = {};
    historico.forEach(transacao => {
      if (!mapaTransacoes[transacao.ativo_id]) {
        mapaTransacoes[transacao.ativo_id] = [];
      }
      mapaTransacoes[transacao.ativo_id].push(transacao);
    });

    tbody.innerHTML = carteira.map(ativo => {
      const transacoes = mapaTransacoes[ativo.ativo_id] || [];
      const botoesHtml = transacoes.map(t => `
        <button type="button" class="btn btn-danger btn-sm btn-apagar" data-transacao-id="${t.transacao_id}" title="Apagar esta transação">
          <i class='bx bx-trash'></i>
        </button>
      `).join('');

      return `
        <tr data-ativo-id="${ativo.ativo_id}">
          <td class="transaction-name">${ativo.asset_name} (${ativo.asset_symbol})</td>
          <td class="amount-income align-right">${ativo.total_cotas}</td>
          <td class="align-center">
            <div class="btn-group btn-group-sm" role="group">
              ${botoesHtml}
            </div>
          </td>
        </tr>
      `;
    }).join('');
    
    // Atualiza o gráfico com os dados da carteira
    const chartLabels = carteira.map(ativo => ativo.asset_symbol);
    const chartValores = carteira.map(ativo => parseFloat(ativo.valor_investido) || 0);
    inicializarGrafico(chartLabels, chartValores);
    
    // Calcula valorização com base nos preços atuais obtidos da BRAPI (se disponíveis)
    try {
      calcularValorizacao(carteira);
    } catch (erro) {
      console.warn('Erro ao calcular valorização:', erro);
    }
    
    // Reattach listeners para os botões de apagar após atualizar a tabela
    anexarListenersApagar();
  }

  /**
   * Calcula a valorização total da carteira comparando preço atual x cotas com o valor investido
   * Se o preço atual não estiver disponível no cache `objFiltro`, usa o `valor_medio` como fallback.
   */
  function calcularValorizacao(carteira) {
    if (!carteira || !carteira.length) {
      atualizarValorizacaoDOM(0);
      return;
    }

    // Mapa rápido de preços vindos da BRAPI: symbol -> close
    const precoMapa = {};
    if (Array.isArray(objFiltro) && objFiltro.length) {
      objFiltro.forEach(item => {
        if (item && item.ticket) precoMapa[item.ticket.toUpperCase()] = parseFloat(item.valor) || 0;
      });
    }

    let totalValorizacao = 0;

    carteira.forEach(ativo => {
      const symbol = (ativo.asset_symbol || '').toUpperCase();
      const cotas = parseFloat(ativo.total_cotas) || 0;
      const valorInvestido = parseFloat(ativo.valor_investido) || 0;

      // Preço atual: preferir BRAPI, senão usar valor médio (valor_medio) como fallback
      let precoAtual = precoMapa[symbol];
      if (!precoAtual || precoAtual === 0) {
        precoAtual = parseFloat(ativo.valor_medio) || 0;
      }

      const valorMercado = precoAtual * cotas;
      const diff = valorMercado - valorInvestido;
      totalValorizacao += diff;
    });

    atualizarValorizacaoDOM(totalValorizacao);
  }

  function atualizarValorizacaoDOM(valor) {
    const el = document.getElementById('valorizacao-valor');
    if (!el) return;
    const sinal = valor >= 0 ? '' : '-';
    const absVal = Math.abs(valor);
    const texto = (isNaN(absVal) ? 0 : absVal).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    el.textContent = (valor >= 0 ? 'R$ ' : '-R$ ') + texto;

    // Atualiza classe no card (mais robusto que inline styles)
    const card = el.closest('.geral-card-despesas');
    if (card) {
      card.classList.remove('val-positivo', 'val-negativo', 'val-neutro');
      if (valor > 0) card.classList.add('val-positivo');
      else if (valor < 0) card.classList.add('val-negativo');
      else card.classList.add('val-neutro');
      // remove estilo inline caso exista
      el.style.color = '';
    } else {
      // fallback: set inline color
      el.style.color = valor > 0 ? '#28a745' : (valor < 0 ? '#dc3545' : '#000');
    }
  }

  // --- 9. Função para anexar listeners aos botões de apagar ---
  function anexarListenersApagar() {
    const botoesApagar = document.querySelectorAll('.btn-apagar');

    botoesApagar.forEach(botao => {
      botao.addEventListener('click', async (e) => {
        e.preventDefault();

        const transacaoId = botao.getAttribute('data-transacao-id');

        if (!transacaoId) {
          alert('Erro: ID da transação não encontrado');
          return;
        }

        // Confirmação antes de apagar
        if (!confirm('Tem certeza que deseja apagar este investimento?')) {
          return;
        }

        try {
          const resposta = await fetch('./api_investimento.php?acao=apagar', {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              transacao_id: parseInt(transacaoId, 10)
            })
          });

          const resultado = await resposta.json();

          if (resultado.sucesso) {
            alert(resultado.mensagem);
            // Recarrega a carteira após apagar
            carregarCarteira();
          } else {
            alert('Erro: ' + resultado.mensagem);
          }
        } catch (erro) {
          console.error('Erro ao apagar investimento:', erro);
          alert('Erro ao processar a requisição');
        }
      });
    });
  }

  // Carrega a carteira ao inicializar a página
  carregarCarteira();
});