document.addEventListener('DOMContentLoaded', () => {
    const basePath = '';

    // Função para exibir mensagem de toast estilo Agendamentos
    function mostrarAlertaSucesso(mensagem) {
        // Cria o elemento da mensagem se ele não existir
        let alertBox = document.getElementById('toast-sucesso');
        if (!alertBox) {
            alertBox = document.createElement('div');
            alertBox.id = 'toast-sucesso';
            alertBox.style.cssText = `
                background-color: #d1fae5;
                color: #065f46;
                border: 1px solid #a7f3d0;
                padding: 12px 20px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-weight: 500;
                font-size: 0.95rem;
                transition: opacity 0.5s ease;
                opacity: 1;
            `;
            
            // Procura o contêiner principal da página para inserir no topo
            const mainContent = document.querySelector('main') || document.querySelector('.content') || document.body;
            mainContent.insertBefore(alertBox, mainContent.firstChild);
        }

        alertBox.innerText = mensagem;

        // Oculta e remove a mensagem suavemente após 3.5 segundos
        setTimeout(() => {
            alertBox.style.opacity = '0';
            setTimeout(() => {
                if (alertBox.parentNode) {
                    alertBox.parentNode.removeChild(alertBox);
                }
            }, 500);
        }, 3500);
    }

    // Verifica parâmetro na URL vindo do Controller
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('sucesso') === '1') {
        mostrarAlertaSucesso('Prontuário salvo com sucesso!');
        // Limpa o parâmetro da URL sem recarregar a página
        const newUrl = window.location.pathname + (urlParams.get('busca') ? '?busca=' + urlParams.get('busca') : '');
        window.history.replaceState({}, document.title, newUrl);
    }

    // Redirecionamento dos cards
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        card.addEventListener('click', () => {
            const action = card.getAttribute('data-action');

            if (action === 'novo-prontuario') {
                window.location.href = `${basePath}/prontuarios`;
            } else if (action === 'agendar-consulta') {
                window.location.href = `${basePath}/agendamentos`;
            } else if (action === 'cadastrar-paciente') {
                window.location.href = `${basePath}/agendamentos#cadastrar`;
            }
        });
    });

    // Upload de arquivos (Drag and Drop / File Input)
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');

    if (fileInput && fileList) {
        fileInput.addEventListener('change', () => {
            fileList.innerHTML = '';
            Array.from(fileInput.files).forEach(file => {
                const item = document.createElement('div');
                item.className = 'file-item';
                item.innerHTML = `<span>📄 ${file.name}</span> <small style="color: #64748b;">(${(file.size / 1024).toFixed(1)} KB)</small>`;
                fileList.appendChild(item);
            });
        });
    }

    if (dropZone && fileInput) {
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });

        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');

            const files = e.dataTransfer.files;
            if (!files || files.length === 0) return;

            Array.from(files).forEach(file => {
                if (file.size > 5 * 1024 * 1024) {
                    alert(`O arquivo ${file.name} excede o limite de 5MB.`);
                    return;
                }
            });

            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        });
    }

    // Ação do Botão Cancelar
    const btnCancelar = document.getElementById('btnCancelar');
    if (btnCancelar) {
        btnCancelar.addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = `${basePath}/prontuarios`;
        });
    }

    // Salvar no envio do formulário
    const prontuarioForm = document.getElementById('formProntuario') || document.querySelector('form[action$="/prontuarios/salvar"]');
    if (prontuarioForm) {
        prontuarioForm.addEventListener('submit', function () {
            const btnSubmit = document.getElementById('btnSalvar') || this.querySelector('button[type="submit"]');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerText = 'Salvando...';
            }
        });
    }
});