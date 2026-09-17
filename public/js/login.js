form.addEventListener('submit', (e) => {
    limparErros();
    let isValid = true;
    let firstInvalidInput = null;

    if (!validarEmail(emailInput.value)) {
        exibirErro(emailInput, 'Insira um e-mail válido.');
        isValid = false;
        firstInvalidInput = firstInvalidInput || emailInput;
    }

    if (!validarSenha(senhaInput.value)) {
        exibirErro(senhaInput, 'A senha precisa ter no mínimo 8 caracteres.');
        isValid = false;
        firstInvalidInput = firstInvalidInput || senhaInput;
    }

    if (!isValid) {
        e.preventDefault();
        firstInvalidInput?.focus();
    }
});

const validarEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());

const validarSenha = (senha) => senha.trim().length >= 8;