// Arquivo: Meta.js

/**
 * Formata uma string de data (ex: 2025-11-01) para "nov/25"
 * @param {string} dateString
 * @returns {string}
 */

const formatChartDate = (dateString) => {
    if (!dateString) return '?';

    try {
        const dateOnlyString = dateString.split(' ')[0];
        const parts = dateOnlyString.split('-');

        if (parts.length !== 3) return '?';

        const date = new Date(parts[0], parts[1] - 1, parts[2]);

        return date.toLocaleDateString('pt-BR', { month: 'short', year: '2-digit' });

    } catch (e) {
        console.error('Erro ao formatar data:', dateString, e);
        return '?';
    }
};

const createChartData = (label, labels, dataValues, borderColor, maxGoal) => ({
    labels: labels,
    datasets: [
        {
            label: label,
            data: dataValues,
            borderColor: borderColor,
            backgroundColor: `${borderColor.replace(")", ", 0.1)").replace("rgb", "rgba")}`,
            borderWidth: 2,
            tension: 0.4,
            fill: 'origin',
            pointRadius: 3,
            pointBackgroundColor: borderColor,
        },
    ],
    maxGoal: maxGoal,
});

const createChartOptions = () => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            mode: "index",
            intersect: false,
            callbacks: {
                label: function (context) {
                    let label = context.dataset.label || "";
                    if (label) label += ": ";
                    if (context.parsed.y !== null) {
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
            grid: { color: "#f0f0f0", borderDash: [5, 5] },
            ticks: {
                callback: function (value, index, values) {
                    if (index === 0 || index === values.length - 1) {
                        return "R$ " + value.toLocaleString("pt-BR", { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                    }
                    return "";
                },
            },
            min: 0,
        },
        x: { grid: { display: false } },
    },
});

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

// =======================================================
// LÓGICA DE VALIDAÇÃO DE FORMULÁRIO
// =======================================================

const clearErrors = (form) => {
    form.querySelectorAll('.form-group.error').forEach(group => {
        group.classList.remove('error');
    });
    form.querySelectorAll('.error-message').forEach(msg => {
        msg.remove();
    });
};

const displayError = (input, message) => {
    const formGroup = input.closest('.form-group');
    if (!formGroup) return;

    formGroup.classList.add('error');

    let errorMessage = formGroup.querySelector('.error-message');
    if (!errorMessage) {
        errorMessage = document.createElement('span');
        errorMessage.className = 'error-message';
        input.parentNode.insertBefore(errorMessage, input.nextSibling);
    }
    errorMessage.textContent = message;
};

const validateForm = (data) => {
    let isValid = true;
    const form = document.getElementById('newMetaForm');
    clearErrors(form);

    const nome = data.get('nome');
    if (!nome || nome.trim().length < 3) {
        displayError(form.elements['nome'], 'O nome da meta deve ter pelo menos 3 caracteres.');
        isValid = false;
    }

    const objetivoInput = form.elements['objetivo'];
    const objetivo = parseFloat(data.get('objetivo'));
    if (isNaN(objetivo) || objetivo <= 0) {
        displayError(objetivoInput, 'O Valor Objetivo deve ser um número positivo.');
        isValid = false;
    }

    const mensalInput = form.elements['mensal'];
    const mensal = parseFloat(data.get('mensal'));
    if (isNaN(mensal) || mensal <= 0) {
        displayError(mensalInput, 'A Contribuição Mensal deve ser um número positivo.');
        isValid = false;
    } else if (objetivoInput && objetivo > 0 && mensal >= objetivo) {
        displayError(mensalInput, 'A contribuição mensal não pode ser maior ou igual ao Objetivo.');
        isValid = false;
    }

    const prazoInput = form.elements['prazo'];
    const prazo = new Date(data.get('prazo'));
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);
    if (isNaN(prazo.getTime())) {
        displayError(prazoInput, 'Por favor, selecione um prazo válido.');
        isValid = false;
    } else if (prazo <= hoje) {
        displayError(prazoInput, 'O prazo deve ser uma data futura.');
        isValid = false;
    }

    const categoria = data.get('categoria');
    if (!categoria) {
        displayError(form.elements['categoria'], 'Selecione uma categoria.');
        isValid = false;
    }

    return isValid;
};

// =======================================================
// LÓGICA DE ENVIO DE FORMULÁRIO (FETCH/AJAX)
// =======================================================

// DEFINA O CAMINHO DA API AQUI PARA FACILITAR
// Se o arquivo que chama esse JS está em /View/Meta.php e o roteador está na raiz /metas.php
// O caminho correto é '../metas.php'
const API_URL = '../metas.php'; 

const handleFormSubmission = async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitButton = form.querySelector('.submit-button');

    if (!validateForm(formData)) {
        const firstErrorInput = form.querySelector('.form-group.error input, .form-group.error select');
        if (firstErrorInput) firstErrorInput.focus();
        return;
    }

    submitButton.textContent = 'Enviando...';
    submitButton.disabled = true;

    const metaData = Object.fromEntries(formData.entries());
    metaData.objetivo = parseFloat(metaData.objetivo);
    metaData.mensal = parseFloat(metaData.mensal);

    try {
        // CORREÇÃO: Usando API_URL
        const response = await fetch(`${API_URL}?action=create`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(metaData)
        });

        // Debug: Se der erro de JSON, mostra o texto puro no console
        const text = await response.text();
        try {
            const result = JSON.parse(text);
            if (result.status === 'success') {
                alert(result.message);
                closeModal();
                location.reload();
            } else {
                alert('Erro: ' + result.message);
            }
        } catch(jsonError) {
            console.error('Resposta não é JSON:', text);
            alert('Erro no servidor. Verifique o console.');
        }

    } catch (error) {
        console.error('Erro ao enviar formulário:', error);
        alert('Ocorreu um erro de conexão.');
    } finally {
        submitButton.textContent = 'Criar Meta';
        submitButton.disabled = false;
    }
};

// =======================================================
// UI E INICIALIZAÇÃO
// =======================================================

let modal, form, firstFocusableElement, lastFocusableElement;

const handleTabKey = (e) => {
    const isTabPressed = (e.key === 'Tab' || e.keyCode === 9);
    if (!isTabPressed || !modal || !firstFocusableElement || !lastFocusableElement) return;

    if (e.shiftKey) {
        if (document.activeElement === firstFocusableElement) {
            lastFocusableElement.focus();
            e.preventDefault();
        }
    } else {
        if (document.activeElement === lastFocusableElement) {
            firstFocusableElement.focus();
            e.preventDefault();
        }
    }
};

const openModal = () => {
    if (modal) {
        modal.style.display = "flex";
        modal.addEventListener('keydown', handleTabKey);
        setTimeout(() => {
            modal.classList.add("open");
            if (firstFocusableElement) firstFocusableElement.focus();
        }, 10);
    }
};

const closeModal = () => {
    if (modal) {
        modal.classList.remove("open");
        modal.removeEventListener('keydown', handleTabKey);
        if (form) {
            form.reset();
            clearErrors(form);
        }
        setTimeout(() => { modal.style.display = "none"; }, 300);
    }
};

const contributeModal = document.getElementById("contributeModal");
const contributeForm = document.getElementById("contributeForm");
const contributeCloseButton = document.getElementById("closeContributeModal");

const openContributeModal = (metaId, metaName) => {
    if (contributeModal) {
        document.getElementById('meta-id-input').value = metaId;
        document.getElementById('meta-name-display').textContent = metaName;
        contributeModal.style.display = "flex";
        setTimeout(() => {
            contributeModal.classList.add("open");
            document.getElementById('contribution-amount').focus();
        }, 10);
    }
};

const closeContributeModal = () => {
    if (contributeModal) {
        contributeModal.classList.remove("open");
        if (contributeForm) contributeForm.reset();
        setTimeout(() => { contributeModal.style.display = "none"; }, 300);
    }
};

const handleContributeSubmission = async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitButton = form.querySelector('.submit-button');

    const metaId = formData.get('meta_id');
    const amount = parseFloat(formData.get('amount'));
    const date = formData.get('date');

    if (isNaN(amount) || amount <= 0) {
        alert('Por favor, insira um valor válido.');
        return;
    }
    if (!date) {
        alert('Selecione a data.');
        return;
    }

    submitButton.textContent = 'Depositando...';
    submitButton.disabled = true;

    try {
        // CORREÇÃO: Usando API_URL
        const response = await fetch(`${API_URL}?action=contribute`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ meta_id: metaId, amount: amount, date: date })
        });

        const text = await response.text();
        const result = JSON.parse(text);

        if (result.status === 'success') {
            alert(result.message);
            closeContributeModal();
            location.reload();
        } else {
            alert('Erro: ' + result.message);
        }
    } catch (error) {
        console.error('Erro de conexão:', error);
        alert('Ocorreu um erro de conexão.');
    } finally {
        submitButton.textContent = 'Confirmar Aporte';
        submitButton.disabled = false;
    }
};

window.removeContribution = async (metaId, date, value) => {
    if (!confirm(`Remover registro de ${date}?`)) return;

    try {
        // CORREÇÃO: Usando API_URL
        const response = await fetch(`${API_URL}?action=undo`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ meta_id: metaId, date: date, value: value })
        });
        const result = await response.json();
        if (result.status === 'success') {
            alert(result.message);
            location.reload();
        } else {
            alert('Erro: ' + result.message);
        }
    } catch (error) {
        alert('Erro de conexão.');
    }
};

document.addEventListener("DOMContentLoaded", () => {
    // Menu Hamburguer
    const hamburger = document.querySelector(".hamburger");
    const navLinks = document.querySelector(".nav-links");
    if (hamburger && navLinks) {
        hamburger.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            navLinks.classList.toggle("active");
        });
    }

    // Scroll Header
    const header = document.querySelector("header");
    window.addEventListener("scroll", () => {
        if (window.scrollY > 20) header.classList.add("scrolled");
        else header.classList.remove("scrolled");
    });

    // Gráficos
    const metaCards = document.querySelectorAll(".card-details");
    metaCards.forEach(card => {
        const id = card.getAttribute('data-meta-id');
        const goal = parseFloat(card.getAttribute('data-meta-goal'));
        let historyData = [];
        try {
            historyData = JSON.parse(card.getAttribute('data-meta-history'));
        } catch(e) { console.log('Erro parsing JSON history'); }

        const label = card.querySelector('.goal-info h2')?.textContent || 'Meta';
        const borderColor = card.querySelector('.progress-bar')?.style.backgroundColor || '#2a68ff';

        let labels, values;
        if (Array.isArray(historyData) && historyData.length > 0) {
             // Garante ordenação por data
             historyData.sort((a, b) => new Date(a.date) - new Date(b.date));
             labels = historyData.map(item => formatChartDate(item.date));
             values = historyData.map(item => item.value);
        } else {
            labels = ['Início']; values = [0];
        }

        const chartData = createChartData(label, labels, values, borderColor, goal);
        createChart(`chartMeta-${id}`, chartData);
    });

    // Modais
    const addMetaButton = document.querySelector(".add-meta-button");
    const closeModalButton = document.getElementById("closeModal");
    modal = document.getElementById("addMetaModal");
    form = document.getElementById("newMetaForm");

    if (modal) {
        const focusable = modal.querySelectorAll('button, input, select, textarea');
        if(focusable.length) {
            firstFocusableElement = focusable[0];
            lastFocusableElement = focusable[focusable.length - 1];
        }
    }

    if (addMetaButton && modal && form) {
        addMetaButton.addEventListener("click", openModal);
        if(closeModalButton) closeModalButton.addEventListener("click", closeModal);
        modal.addEventListener("click", (e) => { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
        form.addEventListener("submit", handleFormSubmission);
    }

    if (contributeModal && contributeForm) {
        if(contributeCloseButton) contributeCloseButton.addEventListener("click", closeContributeModal);
        contributeModal.addEventListener("click", (e) => { if (e.target === contributeModal) closeContributeModal(); });
        document.querySelectorAll('.contribute-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const metaId = e.currentTarget.getAttribute('data-meta-id');
                const metaName = e.currentTarget.getAttribute('data-meta-name');
                openContributeModal(metaId, metaName);
            });
        });
        contributeForm.addEventListener("submit", handleContributeSubmission);
    }

    // Modal Exclusão
    const deleteModal = document.getElementById("deleteConfirmModal");
    const deleteForm = document.getElementById("deleteConfirmForm");
    const deleteMetaIdInput = document.getElementById("delete-meta-id-input");
    const deleteMetaNameDisplay = document.getElementById("delete-meta-name-display");
    const confirmDeleteButton = document.getElementById("confirmDeleteButton");
    const API_URL = '../metas.php';

    const openDeleteModal = (metaId, metaName) => {
        if (deleteModal) {
            deleteMetaIdInput.value = metaId;
            deleteMetaNameDisplay.textContent = metaName;
            deleteModal.style.display = "flex";
            setTimeout(() => { deleteModal.classList.add("open"); }, 10);
        }
    };
    
    const closeDeleteModal = () => {
        if(deleteModal) {
            deleteModal.classList.remove("open");
            setTimeout(() => { deleteModal.style.display = "none"; }, 300);
        }
    };

    document.querySelectorAll('.delete-meta-button').forEach(btn => {
        btn.addEventListener('click', (e) => {
            openDeleteModal(e.currentTarget.getAttribute('data-meta-id'), e.currentTarget.getAttribute('data-meta-name'));
        });
    });

    if(document.getElementById("closeDeleteModal")) document.getElementById("closeDeleteModal").addEventListener("click", closeDeleteModal);
    if(document.getElementById("cancelDeleteButton")) document.getElementById("cancelDeleteButton").addEventListener("click", closeDeleteModal);

    if (deleteForm) {
        deleteForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const metaId = deleteMetaIdInput.value;
            confirmDeleteButton.textContent = "Excluindo...";
            confirmDeleteButton.disabled = true;

            try {
                // CORREÇÃO: Usando API_URL
                const response = await fetch(`${API_URL}?action=delete`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ meta_id: metaId })
                });
                const result = await response.json();
                if (result.status === 'success') {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Erro: ' + result.message);
                }
            } catch (err) {
                alert('Erro de conexão.');
            } finally {
                confirmDeleteButton.textContent = "Confirmar Exclusão";
                confirmDeleteButton.disabled = false;
            }
        });
    }

    // Histórico
    const historyModal = document.getElementById("historyModal");
    const historyList = document.getElementById("history-list");
    const historyTitle = document.getElementById("history-meta-name");

    const renderHistory = (historyData, metaId) => {
        historyList.innerHTML = "";
        historyData.sort((a, b) => new Date(b.date) - new Date(a.date));
        
        historyData.forEach((item) => {
            const li = document.createElement("li");
            li.className = "history-item";
            const valFmt = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(item.value);
            const dateFmt = item.date.split('-').reverse().join('/');
            
            // Só mostra botão de lixo se tiver 'amount' (significa que é aporte manual, não o inicial)
            let deleteBtn = '';
            if(item.amount) {
                 deleteBtn = `<button class="btn-remove-history" onclick="removeContribution(${metaId}, '${item.date}', ${item.value})"><i class='bx bx-trash'></i></button>`;
            }

            li.innerHTML = `
                <div class="history-info">
                    <span class="history-date">${dateFmt}</span>
                    <span class="history-value">Saldo: ${valFmt}</span>
                </div>
                ${deleteBtn}
            `;
            historyList.appendChild(li);
        });
        if (historyData.length === 0) historyList.innerHTML = "<li style='padding:1rem;'>Sem histórico.</li>";
    };

    document.querySelectorAll('.history-button').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const metaId = e.currentTarget.getAttribute('data-meta-id');
            const metaName = e.currentTarget.getAttribute('data-meta-name');
            let historyData = [];
            try { historyData = JSON.parse(e.currentTarget.getAttribute('data-meta-history')); } catch(e){}

            historyTitle.textContent = metaName;
            renderHistory(historyData, metaId);
            historyModal.classList.add("open");
            historyModal.style.display = 'flex';
        });
    });

    if(document.getElementById("closeHistoryModal")) {
        document.getElementById("closeHistoryModal").addEventListener('click', () => {
            historyModal.classList.remove("open");
            setTimeout(() => { historyModal.style.display = 'none'; }, 300);
        });
    }
});