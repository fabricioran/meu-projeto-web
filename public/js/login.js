document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('formLogin');
    var emailInput = document.getElementById('email');
    var senhaInput = document.getElementById('senha');
    var gBtn = document.querySelector('.g-btn');

    // Ação e feedback ao clicar no botão do Google/Gmail
    if (gBtn) {
        gBtn.addEventListener('click', function (e) {
            this.style.opacity = '0.7';
            setTimeout(function () {
                if (gBtn) gBtn.style.opacity = '1';
            }, 150);
        });
    }

    if (!form) return;

    form.addEventListener('submit', function (e) {
        var isValid = true;
        
        limparErros();

        var regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regexEmail.test(emailInput.value.trim())) {
            isValid = false;
            exibirErro(emailInput, 'Insira um e-mail válido.');
        }

        if (senhaInput.value.trim().length < 6) {
            isValid = false;
            exibirErro(senhaInput, 'A senha precisa ter no mínimo 6 caracteres.');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    function exibirErro(elemento, mensagem) {
        elemento.classList.add('input-error');
        var span = document.createElement('span');
        span.className = 'error-msg';
        span.textContent = mensagem;
        elemento.parentNode.appendChild(span);
    }

    function limparErros() {
        var msgs = document.querySelectorAll('.error-msg');
        for (var i = 0; i < msgs.length; i++) {
            msgs[i].remove();
        }
        emailInput.classList.remove('input-error');
        senhaInput.classList.remove('input-error');
    }
});