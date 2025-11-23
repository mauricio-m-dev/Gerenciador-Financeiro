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
        legend: {
            display: false,
        },
        tooltip: {
            mode: "index",
            intersect: false,
            callbacks: {
                label: function (context) {
                    let label = context.dataset.label || "";
                    if (label) {
                        label += ": ";
                    }
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
            grid: {
                color: "#f0f0f0",
                borderDash: [5, 5],
            },
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
        x: {
            grid: {
                display: false,
            },
        },
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
// LÓGICA DE VALIDAÇÃO DE FORMULÁRIO (MANTIDO CONFORME ORIGINAL)
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
        displayError(objetivoInput, 'O Valor Objetivo deve ser um número positivo (maior que zero).');
        isValid = false;
    }

    const mensalInput = form.elements['mensal'];
    const mensal = parseFloat(data.get('mensal'));
    if (isNaN(mensal) || mensal <= 0) {
        displayError(mensalInput, 'A Contribuição Mensal deve ser um número positivo (maior que zero).');
        isValid = false;
    } else if (objetivoInput && objetivo > 0 && mensal >= objetivo) {
        displayError(mensalInput, 'A contribuição mensal não pode ser maior ou igual ao Objetivo total.');
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
// !! MUDANÇA PRINCIPAL AQUI !!
// LÓGICA DE ENVIO DE FORMULÁRIO (AGORA USA FETCH/AJAX)
// =======================================================

/**
 * Manipula a submissão do formulário, enviando os dados para o servidor PHP.
 * @param {Event} e - O evento de submissão.
 */
const handleFormSubmission = async (e) => { // Adicionamos 'async' para usar 'await'
    e.preventDefault(); // Impede o recarregamento padrão da página

    const form = e.target;
    const formData = new FormData(form);
    const submitButton = form.querySelector('.submit-button');

    // 1. Validação (Função que já existia)
    if (!validateForm(formData)) {
        const firstErrorInput = form.querySelector('.form-group.error input, .form-group.error select');
        if (firstErrorInput) {
            firstErrorInput.focus();
        }
        return; // Para a execução se o formulário for inválido
    }

    // 2. Feedback visual (desabilita o botão)
    submitButton.textContent = 'Enviando...';
    submitButton.disabled = true;

    // 3. Preparação dos dados para envio (Converte FormData em Objeto)
    // Usamos Object.fromEntries para pegar todos os campos (nome, objetivo, cor, etc.)
    const metaData = Object.fromEntries(formData.entries());

    // Converte os valores de dinheiro para números (o PHP espera números)
    metaData.objetivo = parseFloat(metaData.objetivo);
    metaData.mensal = parseFloat(metaData.mensal);

    // 4. Envio para o Servidor (A Mágica do AJAX/Fetch)
    // Usamos try...catch...finally para lidar com sucesso, erro de rede, e reabilitar o botão
    try {
        // Envia os dados para 'metas.php?action=create' (o roteador que criamos)
        const response = await fetch('metas.php?action=create', {
            method: 'POST',
            headers: {
                // Informa ao PHP que estamos enviando JSON
                'Content-Type': 'application/json',
            },
            // Converte o objeto JavaScript em uma string JSON
            body: JSON.stringify(metaData)
        });

        // Espera a resposta do PHP (que também será JSON)
        const result = await response.json();

        // 5. Tratamento da Resposta do Servidor
        if (result.status === 'success') {
            // Se o PHP retornar sucesso:
            alert(result.message); // Ex: "Meta criada com sucesso!"
            closeModal(); // Fecha o modal (esta função já limpa o formulário)

            // IMPORTANTE: Recarrega a página para que a nova meta (vinda do BD) apareça
            location.reload();

        } else {
            // Se o PHP retornar um erro (ex: falha no BD ou validação):
            alert('Erro: ' + result.message);
        }

    } catch (error) {
        // Se houver um erro de rede (ex: servidor caiu, sem internet)
        console.error('Erro ao enviar formulário:', error);
        alert('Ocorreu um erro de conexão. Tente novamente.');
    } finally {
        // 6. 'finally' sempre executa, reabilitando o botão
        submitButton.textContent = 'Criar Meta';
        submitButton.disabled = false;
    }
};


// =======================================================
// LÓGICA DE UI E INICIALIZAÇÃO (MANTIDO CONFORME ORIGINAL)
// =======================================================

let modal, form, firstFocusableElement, lastFocusableElement;

const handleTabKey = (e) => {
    const isTabPressed = (e.key === 'Tab' || e.keyCode === 9);

    if (!isTabPressed || !modal || !firstFocusableElement || !lastFocusableElement) {
        return;
    }

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
            if (firstFocusableElement) {
                firstFocusableElement.focus();
            }
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
        setTimeout(() => {
            modal.style.display = "none";
        }, 300);
    }
};


const contributeModal = document.getElementById("contributeModal");
const contributeForm = document.getElementById("contributeForm");
const contributeCloseButton = document.getElementById("closeContributeModal");

// Abre o modal de aporte
const openContributeModal = (metaId, metaName) => {
    if (contributeModal) {
        // Preenche o ID e o Nome da meta no modal
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
        setTimeout(() => {
            contributeModal.style.display = "none";
        }, 300);
    }
};

/**
 * Envia o aporte para o servidor.
 */
const handleContributeSubmission = async (e) => {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const submitButton = form.querySelector('.submit-button');

    const metaId = formData.get('meta_id');
    const amount = parseFloat(formData.get('amount'));
    const date = formData.get('date');


    if (isNaN(amount) || amount <= 0) {
        alert('Por favor, insira um valor de aporte válido.');
        return;
    }

    if (!date) {
        alert('Por favor, selecione o mês e ano do aporte.');
        document.getElementById('contribution-date').focus();
        return;
    }



    submitButton.textContent = 'Depositando...';
    submitButton.disabled = true;

    try {
        const response = await fetch('metas.php?action=contribute', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                meta_id: metaId,
                amount: amount,
                date: date
            })
        });

        const result = await response.json();

        if (result.status === 'success') {
            alert(result.message);
            closeContributeModal();
            location.reload(); // Recarrega para ver o novo acumulado e gráfico
        } else {
            alert('Erro ao registrar aporte: ' + result.message);
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
    if (!confirm(`Deseja remover o registro de ${date}? Isso recalculará o saldo total.`)) {
        return;
    }

    try {
        const response = await fetch('metas.php?action=undo', {
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
        console.error(error);
        alert('Erro de conexão.');
    }
};



document.addEventListener("DOMContentLoaded", () => {
    // 1. Funcionalidade do Menu Hamburguer
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


    // 2. Sombra no Header ao Rolar
    const header = document.querySelector("header");

    window.addEventListener("scroll", () => {
        if (window.scrollY > 20) {
            header.classList.add("scrolled");
        } else {
            header.classList.remove("scrolled");
        }
    });

    // 3. Inicialização dos Gráficos
    const metaCards = document.querySelectorAll(".card-details");

    metaCards.forEach(card => {
        const id = card.getAttribute('data-meta-id');
        const goal = parseFloat(card.getAttribute('data-meta-goal'));
        // Lê os dados históricos e converte de volta para array JavaScript
        const historyData = JSON.parse(card.getAttribute('data-meta-history'));

        // Como não temos a label no JS, você pode tentar pegá-la de um H2 próximo ou passar via data-attr
        const label = card.querySelector('.goal-info h2')?.textContent || 'Meta';
        const borderColor = card.querySelector('.progress-bar')?.style.backgroundColor || '#2a68ff';

        let labels, values;

        if (Array.isArray(historyData) && historyData.length > 0) {

            if (typeof historyData[0] === 'object' && historyData[0] !== null && 'value' in historyData[0]) {

                historyData.sort((a, b) => new Date(a.date) - new Date(b.date));
                labels = historyData.map(item => formatChartDate(item.date));
                values = historyData.map(item => item.value);
            } else {
                values = historyData;
                labels = Array.from({ length: values.length }, (_, i) => `Aporte ${i}`);
            }
        } else {
            labels = ['Início'];
            values = [0];
        }

        const chartData = createChartData(
            label,
            labels,
            values,
            borderColor,
            goal
        );
        createChart(`chartMeta-${id}`, chartData);
    });


    // 4. Lógica do Modal com Acessibilidade e Validação
    const addMetaButton = document.querySelector(".add-meta-button");
    const closeModalButton = document.getElementById("closeModal");

    modal = document.getElementById("addMetaModal");
    form = document.getElementById("newMetaForm");

    if (modal) {
        const focusableModalElements = modal.querySelectorAll('button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])');

        firstFocusableElement = focusableModalElements.length > 0 ? focusableModalElements[0] : null;
        lastFocusableElement = focusableModalElements.length > 0 ? focusableModalElements[focusableModalElements.length - 1] : null;
    }


    if (addMetaButton && modal && closeModalButton && form) {
        addMetaButton.addEventListener("click", openModal);
        closeModalButton.addEventListener("click", closeModal);

        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('open')) {
                closeModal();
            }
        });

        // !! PONTO IMPORTANTE !!
        // Aqui é onde o evento de 'submit' do formulário é conectado
        // à nova função 'handleFormSubmission' que acabamos de reescrever.
        form.addEventListener("submit", handleFormSubmission);

    } else {
        console.error("Erro: Elementos do Modal não encontrados no DOM.");
    }


    if (contributeModal && contributeForm && contributeCloseButton) {
        contributeCloseButton.addEventListener("click", closeContributeModal);

        contributeModal.addEventListener("click", (e) => {
            if (e.target === contributeModal) {
                closeContributeModal();
            }
        });

        // Evento para o botão "Registrar Aporte" em cada cartão
        document.querySelectorAll('.contribute-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const metaId = e.currentTarget.getAttribute('data-meta-id');
                const metaName = e.currentTarget.getAttribute('data-meta-name');
                openContributeModal(metaId, metaName);
            });
        });

        // Evento de submissão do formulário de aporte
        contributeForm.addEventListener("submit", handleContributeSubmission);
    }

    //  Pega os elementos do novo modal
    const deleteModal = document.getElementById("deleteConfirmModal");
    const deleteForm = document.getElementById("deleteConfirmForm");
    const closeDeleteModalButton = document.getElementById("closeDeleteModal");
    const cancelDeleteButton = document.getElementById("cancelDeleteButton");
    const confirmDeleteButton = document.getElementById("confirmDeleteButton");
    const deleteMetaIdInput = document.getElementById("delete-meta-id-input");
    const deleteMetaNameDisplay = document.getElementById("delete-meta-name-display");

    //  Função para abrir o modal de exclusão
    const openDeleteModal = (metaId, metaName) => {
        if (deleteModal) {
            deleteMetaIdInput.value = metaId;
            deleteMetaNameDisplay.textContent = metaName; // Mostra o nome da meta

            deleteModal.style.display = "flex";
            setTimeout(() => {
                deleteModal.classList.add("open");
                confirmDeleteButton.focus(); // Foca no botão de confirmação
            }, 10);
        }
    };

    //  Função para fechar o modal
    const closeDeleteModal = () => {
        if (deleteModal) {
            deleteModal.classList.remove("open");
            setTimeout(() => {
                deleteModal.style.display = "none";
                deleteMetaIdInput.value = "";
                deleteMetaNameDisplay.textContent = "";
            }, 300);
        }
    };

    // Adiciona listener para todos os botões "Excluir"
    document.querySelectorAll('.delete-meta-button').forEach(button => {
        button.addEventListener('click', (e) => {
            const metaId = e.currentTarget.getAttribute('data-meta-id');
            const metaName = e.currentTarget.getAttribute('data-meta-name');
            openDeleteModal(metaId, metaName);
        });
    });

    //  Adiciona listeners para fechar o modal
    if (closeDeleteModalButton) closeDeleteModalButton.addEventListener("click", closeDeleteModal);
    if (cancelDeleteButton) cancelDeleteButton.addEventListener("click", closeDeleteModal);

    if (deleteModal) {
        deleteModal.addEventListener("click", (e) => {
            if (e.target === deleteModal) {
                closeDeleteModal();
            }
        });
    }

    // Listener para a submissão (clique em "Confirmar Exclusão")
    if (deleteForm) {
        deleteForm.addEventListener("submit", async (e) => {
            e.preventDefault(); // Impede o envio do formulário

            const metaId = deleteMetaIdInput.value;
            if (!metaId) return;

            confirmDeleteButton.textContent = "Excluindo...";
            confirmDeleteButton.disabled = true;

            try {
                const response = await fetch('metas.php?action=delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        meta_id: metaId
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    alert(result.message);
                    closeDeleteModal();
                    location.reload(); // Recarrega a página para remover a meta
                } else {
                    alert('Erro ao excluir: ' + result.message);
                }

            } catch (error) {
                console.error('Erro de conexão:', error);
                alert('Ocorreu um erro de conexão.');
            } finally {
                confirmDeleteButton.textContent = 'Confirmar Exclusão';
                confirmDeleteButton.disabled = false;
            }
        });
    }


    const historyModal = document.getElementById("historyModal");
    const historyList = document.getElementById("history-list");
    const closeHistoryBtn = document.getElementById("closeHistoryModal");
    const historyTitle = document.getElementById("history-meta-name");

    const formatCurrency = (value) => {
        return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
    };

    const formatDate = (dateString) => {
        const [year, month, day] = dateString.split('-');
        return `${day}/${month}/${year}`;
    };

    // Função para renderizar a lista
    const renderHistory = (historyData, metaId) => {
        historyList.innerHTML = "";
        
        // Ordena do mais recente para o mais antigo
        historyData.sort((a, b) => new Date(b.date) - new Date(a.date));

        historyData.forEach((item, index) => {
            // Calcula o valor do aporte (diferença do item atual para o anterior no tempo cronológico)
            // OBS: Como invertemos a lista para exibição (b - a), precisamos ter cuidado na lógica do delta visual
            // Mas para exclusão, usamos apenas os dados brutos.
            
            const li = document.createElement("li");
            li.className = "history-item";
            
            li.innerHTML = `
                <div class="history-info">
                    <span class="history-date">${formatDate(item.date)}</span>
                    <span class="history-value">Saldo: ${formatCurrency(item.value)}</span>
                </div>
                <button class="btn-remove-history" onclick="removeContribution(${metaId}, '${item.date}', ${item.value})">
                    <i class='bx bx-trash'></i>
                </button>
            `;
            historyList.appendChild(li);
        });
        
        if (historyData.length === 0) {
            historyList.innerHTML = "<li style='padding:1rem; text-align:center'>Sem histórico.</li>";
        }
    };


    document.querySelectorAll('.history-button').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const metaId = e.currentTarget.getAttribute('data-meta-id');
            const metaName = e.currentTarget.getAttribute('data-meta-name');
            const historyRaw = e.currentTarget.getAttribute('data-meta-history');
            
            const historyData = JSON.parse(historyRaw);
            
            historyTitle.textContent = metaName;
            renderHistory(historyData, metaId);
            
            historyModal.classList.add("open");
            historyModal.style.display = 'flex'; // Garante display flex
        });
    });

    // Fechar Modal
    const closeHistory = () => {
        historyModal.classList.remove("open");
        setTimeout(() => { historyModal.style.display = 'none'; }, 300);
    };
    
    if (closeHistoryBtn) closeHistoryBtn.addEventListener('click', closeHistory);
    
    window.onclick = (event) => {
        if (event.target == historyModal) {
            closeHistory();
        }
    };

});