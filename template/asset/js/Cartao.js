document.addEventListener('DOMContentLoaded', function () {
    const cardPlaceholders = document.querySelectorAll('.card-carteira');
    let expenseChart = null;
    const BASE_COLORS = ['#EF4438', '#9B51E0', '#4CAF50', '#2D9CDB', '#F2C94C', '#F2994A'];

    // --- ATUALIZAR DADOS NA TELA ---
    function updateDashboard(data) {
        if (data.sumario) {
            const els = {
                renda: document.getElementById('renda-valor'),
                despesa: document.getElementById('despesas-valor'),
                meta: document.getElementById('metas-valor')
            };
            if(els.renda) els.renda.textContent = data.sumario.renda;
            if(els.despesa) els.despesa.textContent = data.sumario.despesa;
            if(els.meta) els.meta.textContent = data.sumario.meta;
        }

        if (expenseChart && data.grafico) {
            const vals = data.grafico.data.map(Number);
            const isEmpty = vals.length === 0 || vals.every(v => v === 0);
            
            expenseChart.data.labels = isEmpty ? ['Sem dados'] : data.grafico.labels;
            expenseChart.data.datasets[0].data = isEmpty ? [1] : vals;
            expenseChart.data.datasets[0].backgroundColor = isEmpty ? ['#e0e0e0'] : BASE_COLORS;
            expenseChart.update();
        }
    }

    function fetchCardData(cardId) {
        const loading = document.getElementById('despesas-valor');
        if(loading) loading.style.opacity = '0.5';

        fetch(`../config/api_cartao.php?card_id=${cardId}`)
            .then(r => r.json())
            .then(data => {
                if(!data.error) updateDashboard(data);
                else console.error(data.error);
            })
            .catch(e => console.error(e))
            .finally(() => { if(loading) loading.style.opacity = '1'; });
    }

    function setActive(index) {
        cardPlaceholders.forEach((el, i) => {
            if (i === index) {
                el.classList.add('active');
                el.style.border = "2px solid #6C5CE7";
                el.style.transform = "scale(1.02)";
                fetchCardData(el.getAttribute('data-card-id'));
            } else {
                el.classList.remove('active');
                el.style.border = "none";
                el.style.borderLeft = el.style.borderLeft; 
                el.style.transform = "scale(1)";
            }
        });
    }

    // Listeners
    cardPlaceholders.forEach((el, i) => el.addEventListener('click', () => setActive(i)));

    // --- CHART JS INIT ---
    const ctx = document.getElementById('expenseDoughnutChart');
    if (ctx) {
        let labels=[], vals=[];
        try {
            labels = JSON.parse(ctx.dataset.labels||'[]');
            vals = JSON.parse(ctx.dataset.valores||'[]');
        } catch(e){}

        const numVals = vals.map(Number);
        const isEmpty = numVals.length===0 || numVals.every(n=>n===0);

        expenseChart = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: isEmpty ? ['Sem dados'] : labels,
                datasets: [{
                    data: isEmpty ? [1] : numVals,
                    backgroundColor: isEmpty ? ['#e0e0e0'] : BASE_COLORS,
                    borderWidth: 0
                }]
            },
            options: { responsive: true, cutout: '75%', plugins: { legend: {display:false} } }
        });
    }

    // --- FORMS ---
    // Mask
    const inpNum = document.getElementById('numeroCartao');
    if(inpNum) inpNum.addEventListener('input', e=>e.target.value=e.target.value.replace(/\D/g,'').replace(/(.{4})/g,'$1 ').trim());
    
    // Limite toggle
    const selTipo = document.getElementById('tipoCartao');
    const divLim = document.getElementById('divLimite');
    if(selTipo) selTipo.addEventListener('change', ()=>divLim.style.display=selTipo.value==='credito'?'block':'none');

    // Submit Add
    const fAdd = document.getElementById('formAddCartao');
    if(fAdd) {
        fAdd.addEventListener('submit', e=>{
            e.preventDefault();
            fetch('../config/salvar_cartao.php', { method:'POST', body:new FormData(fAdd) })
                .then(r=>{ if(r.ok) window.location.reload(); else alert('Erro ao salvar'); });
        });
    }

    // Submit Delete
    const btnDel = document.getElementById('confirmDeleteCardBtn');
    const selDel = document.getElementById('selectCardToDelete');
    if(btnDel && selDel) {
        btnDel.addEventListener('click', ()=>{
            if(!selDel.value) return alert('Selecione um cartão');
            fetch('../config/deletar_cartao.php', {
                method:'POST',
                headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body:`id_cartao=${selDel.value}`
            }).then(r=>r.json()).then(d=>{ if(d.success) window.location.reload(); });
        });
    }
});