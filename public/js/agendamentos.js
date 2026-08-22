document.addEventListener("DOMContentLoaded", function () {
    // Esconde alertas automaticamente após 3 segundos
    var alerts = document.querySelectorAll(".alert");
    if (alerts.length > 0) {
        setTimeout(function () {
            alerts.forEach(function (alertItem) {
                alertItem.style.transition = "opacity 0.5s ease";
                alertItem.style.opacity = "0";
                setTimeout(function () {
                    alertItem.remove();
                }, 500);
            });
        }, 3000);
    }

    var form = document.getElementById("formPaciente");
    if (!form) return;

    var nomeInput = document.getElementById("nome");
    var cpfInput = document.getElementById("cpf");
    var telInput = document.getElementById("telefone");
    var emailInput = document.getElementById("email");

    // Mascara simples de CPF
    if (cpfInput) {
        cpfInput.addEventListener("input", function (e) {
            var v = e.target.value.replace(/\D/g, "");
            if (v.length > 11) v = v.slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, "$1.$2");
            v = v.replace(/(\d{3})(\d)/, "$1.$2");
            v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
            e.target.value = v;
        });
    }

    // Mascara simples de Telefone
    if (telInput) {
        telInput.addEventListener("input", function (e) {
            var v = e.target.value.replace(/\D/g, "");
            if (v.length > 11) v = v.slice(0, 11);
            v = v.replace(/^(\d{2})(\d)/g, "($1) $2");
            v = v.replace(/(\d)(\d{4})$/, "$1-$2");
            e.target.value = v;
        });
    }

    // Limpa erros ao digitar
    var inputs = [nomeInput, cpfInput, telInput, emailInput];
    inputs.forEach(function (input) {
        if (!input) return;
        input.addEventListener("input", function () {
            this.classList.remove("input-error");
            var errSpan = document.getElementById("err-" + this.id);
            if (errSpan) {
                errSpan.innerText = "";
            }
        });
    });

    // Validacao do Formulario
    form.addEventListener("submit", function (e) {
        var isValid = true;

        if (!nomeInput.value.trim()) {
            showError(nomeInput, "Digite o nome completo.");
            isValid = false;
        }

        var cpfClean = cpfInput.value.replace(/\D/g, "");
        if (cpfClean.length !== 11) {
            showError(cpfInput, "CPF deve conter 11 dígitos.");
            isValid = false;
        }

        var telClean = telInput.value.replace(/\D/g, "");
        if (telClean.length < 10) {
            showError(telInput, "Informe um telefone válido.");
            isValid = false;
        }

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value.trim())) {
            showError(emailInput, "Informe um e-mail válido.");
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    function showError(input, message) {
        input.classList.add("input-error");
        var errSpan = document.getElementById("err-" + input.id);
        if (errSpan) {
            errSpan.innerText = message;
        }
    }
});