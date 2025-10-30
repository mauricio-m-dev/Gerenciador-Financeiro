document.addEventListener('DOMContentLoaded', function () {
	const navLinks = document.querySelectorAll('.nav-links a');
	const STORAGE_KEY = 'nav-active-index';

	function setActive(index) {
		navLinks.forEach((a, i) => {
			if (i === index) {
				a.classList.add('active');
				a.setAttribute('aria-current', 'page');
			} else {
				a.classList.remove('active');
				a.removeAttribute('aria-current');
			}
		});
	}

	// Restore from localStorage
	const saved = localStorage.getItem(STORAGE_KEY);
	if (saved !== null) {

  /*
   |--------------------------------------------------------------------------
   | Scroll automático para telas estreitas
   |--------------------------------------------------------------------------
   | Comportamento: quando a largura da janela for <= 843px, rola suavemente
   | para a lista interna de transações `.transactions-list` após o carregamento.
   | Também escuta resize e, ao cruzar o limite para telas estreitas, executa o scroll.
   */
  (function() {
    const THRESHOLD = 843;

    function scrollTransactionsIfNarrow() {
      const list = document.querySelector('.transactions-list');
      if (!list) return;
      if (window.innerWidth <= THRESHOLD) {
        // espera um pequeno atraso para garantir layout completo (imagens/fonts)
        setTimeout(() => {
          try {
            list.scrollTo({ top: list.scrollHeight, behavior: 'smooth' });
          } catch (e) {
            list.scrollTop = list.scrollHeight;
          }
        }, 250);
      }
    }

    // Chama no carregamento
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', scrollTransactionsIfNarrow);
    } else {
      scrollTransactionsIfNarrow();
    }

    // Detecta mudança de tamanho e rola quando cruzar para abaixo do limite
    let lastNarrow = window.innerWidth <= THRESHOLD;
    window.addEventListener('resize', () => {
      const nowNarrow = window.innerWidth <= THRESHOLD;
      if (nowNarrow && !lastNarrow) {
        scrollTransactionsIfNarrow();
      }
      lastNarrow = nowNarrow;
    });
  })();

		const idx = parseInt(saved, 10);
		if (!Number.isNaN(idx) && idx >= 0 && idx < navLinks.length) {
			setActive(idx);
		}
	}

	// Click handlers
	navLinks.forEach((link, idx) => {
		link.addEventListener('click', function (e) {
			// If links are anchors to other pages, let navigation happen.
			// For now they are '#', so prevent default to stay on the page and demonstrate selection.
			if (link.getAttribute('href') === '#') e.preventDefault();
			setActive(idx);
			localStorage.setItem(STORAGE_KEY, String(idx));
		});

		// Allow keyboard Enter/Space to activate
		link.addEventListener('keydown', function (e) {
			if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				link.click();
			}
		});
	});


    // (Botão 'Ver transações' removido; scroll automático da lista ainda ocorre em viewports pequenos)



	/*
  |--------------------------------------------------------------------------
  | Seção 2: Gráfico de Despesas (Chart.js)
  |--------------------------------------------------------------------------
  */
  const ctx = document.getElementById('expenseDoughnutChart');
  
  if (ctx) {
    let chartLabels = [];
    let chartValores = [];

    // Tenta ler os dados dinâmicos injetados pelo PHP (proteção para atributos vazios)
    try {
      chartLabels = JSON.parse(ctx.dataset.labels || '[]');
      chartValores = JSON.parse(ctx.dataset.valores || '[]');
    } catch (e) {
      console.error("Erro ao ler dados do gráfico (JSON inválido):", e);
   
      chartValores = [100];
    }

    // Log para depuração: mostra o que foi lido antes de renderizar
    console.log('expenseDoughnutChart datasets:', chartLabels, chartValores);
  
    // Configuração dos dados do gráfico
    const data = {
      labels: chartLabels,
      datasets: [{
        label: 'Despesas (R$)',
        data: chartValores,
        backgroundColor: [
          '#EF4438', // Casa (Vermelho)
          '#9B51E0', // Cartão (Roxo)
          '#4CAF50', // Transporte (Verde)
          '#2D9CDB', // Mantimentos (Azul)
          '#F2C94C'  // Compras (Amarelo)
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
        cutout: '70%',
        plugins: {
          legend: {
            display: false // Remove a legenda completamente
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return context.label;
              }
            }
          },
          // Desativa os rótulos dentro do gráfico
          datalabels: {
            display: false
          }
        }
      }
    };

    // Registra plugin datalabels se disponível (carregado via CDN)
    if (window.ChartDataLabels) {
      Chart.register(ChartDataLabels);
    }

    // Cria o gráfico
    const ctx2d = ctx.getContext('2d');
    const expenseChart = new Chart(ctx2d, config);

    /* ---------------------------
       Interações adicionais
       - seleção de cartões
       - adicionar cartão
       - realce por categoria no gráfico
       - interação simples na tabela de transações
    --------------------------- */

    // 1) Seleção de card placeholders (salva no localStorage)
    const cardPlaceholders = document.querySelectorAll('.card-placeholder');
    const SELECTED_CARD_KEY = 'selected-card-index';

    function setCardActive(index) {
      cardPlaceholders.forEach((el, i) => {
        if (i === index) el.classList.add('active');
        else el.classList.remove('active');
      });
      localStorage.setItem(SELECTED_CARD_KEY, String(index));
    }

    // Restaura seleção
    const savedCard = localStorage.getItem(SELECTED_CARD_KEY);
    if (savedCard !== null) {
      const idx = parseInt(savedCard, 10);
      if (!Number.isNaN(idx) && idx >= 0 && idx < cardPlaceholders.length) setCardActive(idx);
    }

    cardPlaceholders.forEach((el, i) => {
      el.addEventListener('click', () => setCardActive(i));
    });

    
    // 3) Realce por categoria: ao clicar em item da lista de categorias, destaca a fatia correspondente
    const categoryItems = document.querySelectorAll('.category-item');
    const baseColors = [
      '#EF4438', '#9B51E0', '#4CAF50', '#2D9CDB', '#F2C94C'
    ];

    function dimColor(hex, alpha) {
      // convierte hex para rgba simples
      const h = hex.replace('#', '');
      const bigint = parseInt(h, 16);
      const r = (bigint >> 16) & 255;
      const g = (bigint >> 8) & 255;
      const b = bigint & 255;
      return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    let highlightedIndex = null;
    categoryItems.forEach((li, i) => {
      li.addEventListener('click', () => {
        if (!expenseChart) return;
        if (highlightedIndex === i) {
          // reset
          expenseChart.data.datasets[0].backgroundColor = baseColors.slice(0, expenseChart.data.labels.length);
          highlightedIndex = null;
        } else {
          highlightedIndex = i;
          const newColors = baseColors.slice(0, expenseChart.data.labels.length).map((c, idx) => {
            return idx === i ? c : dimColor(c, 0.18);
          });
          expenseChart.data.datasets[0].backgroundColor = newColors;
        }
        expenseChart.update();
      });
    });

    // 4) Interação na tabela de transações: destaque da linha ao clicar nos três pontos
    const dots = document.querySelectorAll('.transactions-table td i.bx-dots-vertical-rounded');
    dots.forEach(d => {
      d.addEventListener('click', (e) => {
        const tr = e.target.closest('tr');
        if (!tr) return;
        tr.classList.toggle('row-selected');
      });
    });
  }


/*
|--------------------------------------------------------------------------
| 3. Uso da Seleção: Lógica de Filtro
|--------------------------------------------------------------------------
| Esta é uma função de exemplo que você chamaria para atualizar o painel
| quando um cartão é selecionado.
*/
function filterDashboardBySelectedCard() {
    // 1. Obtém o índice do cartão ativo salvo no localStorage
    const savedCardIndex = localStorage.getItem(SELECTED_CARD_KEY); 
    const selectedCard = document.querySelector('.card-placeholder.active'); // Ou pega o elemento ativo
    
    // Se nenhum cartão estiver ativo, o filtro deve mostrar 'todos'
    if (!selectedCard) {
        console.log("Nenhum cartão selecionado. Exibindo dados de todas as contas.");
        // Implementar lógica de 'Mostrar Todos' aqui (ex: re-renderizar transações)
        return; 
    }
    
    // Obtém o ID (ou outro identificador) do cartão
    const cardId = selectedCard.dataset.cardId || `Card ${savedCardIndex}`;
    
    console.log(`Painel filtrado pelo: ${cardId}`);
    
    // AQUI VOCÊ IMPLEMENTARIA A LÓGICA REAL:
    // a) Fazer uma chamada AJAX (fetch) para o backend, enviando 'cardId'
    // b) O backend retornaria novos dados para:
    //    - Atualizar os valores de Renda/Despesa/Metas
    //    - Atualizar os dados do Gráfico (expenseChart.update())
    //    - Atualizar as linhas da Tabela de Transações
}

// ⚠️ Se você quiser que o painel atualize imediatamente após a seleção do cartão:
// Modifique a função de clique na linha 263 do seu código:

/*
// Linha 263:
cardPlaceholders.forEach((el, i) => {
    el.addEventListener('click', () => {
        setCardActive(i);
        filterDashboardBySelectedCard(); // <-- Adiciona a chamada de filtro
    });
});
*/

// Chame o filtro uma vez no carregamento da página para refletir o estado salvo:
// (Você pode adicionar isso logo após o código que restaura a seleção na linha 261)
filterDashboardBySelectedCard();
 // Máscara para número do cartão (XXXX XXXX XXXX XXXX)
  const numeroCartaoInput = document.getElementById('numeroCartao');
  numeroCartaoInput.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.match(/.{1,4}/g);
    if(value) e.target.value = value.join(' ');
  });

  // Limita CVV apenas para números
  const cvvInput = document.getElementById('cvvCartao');
  cvvInput.addEventListener('input', function(e){
    e.target.value = e.target.value.replace(/\D/g, '');
  });

  // Mostrar/ocultar campo de limite conforme tipo de cartão
  const tipoCartaoSelect = document.getElementById('tipoCartao');
  const limiteInput = document.getElementById('limiteCartao');
  function toggleLimite() {
    if(tipoCartaoSelect.value === 'credito') {
      limiteInput.required = true;
      limiteInput.parentElement.style.display = 'block';
    } else {
      limiteInput.required = false;
      limiteInput.parentElement.style.display = 'none';
      limiteInput.value = '';
    }
  }
  tipoCartaoSelect.addEventListener('change', toggleLimite);
  toggleLimite(); // inicial
});
