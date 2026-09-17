document.addEventListener("DOMContentLoaded", () => {
    const alerts = document.querySelectorAll(".alert");
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach((alert) => {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);
    }

    const form = document.getElementById("formPaciente");
    if (!form) return;

    const fields = {
        nome: document.getElementById("nome"),
        cpf: document.getElementById("cpf"),
        telefone: document.getElementById("telefone"),
        email: document.getElementById("email"),
        especialidade: document.getElementById("especialidade"),
        data: document.getElementById("data_agendamento"),
        hora: document.getElementById("hora_agendamento")
    };

    if (fields.cpf) {
        fields.cpf.addEventListener("input", (e) => {
            let v = e.target.value.replace(/\D/g, "").slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, "$1.$2");
            v = v.replace(/(\d{3})(\d)/, "$1.$2");
            v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
            e.target.value = v;
        });
    }

    if (fields.telefone) {
        fields.telefone.addEventListener("input", (e) => {
            let v = e.target.value.replace(/\D/g, "").slice(0, 11);
            v = v.replace(/^(\d{2})(\d)/g, "($1) $2");
            v = v.length > 13 
                ? v.replace(/(\d{5})(\d{4})$/, "$1-$2") 
                : v.replace(/(\d{4})(\d{4})$/, "$1-$2");
            e.target.value = v;
        });
    }

    function validarCPF(cpf) {
        const clean = cpf.replace(/\D/g, "");
        if (clean.length !== 11 || /^(\d)\1{10}$/.test(clean)) return false;

        const calc = (len) => {
            let s = 0;
            for (let i = 0; i < len; i++) {
                s += parseInt(clean.charAt(i), 10) * ((len + 1) - i);
            }
            const r = (s * 10) % 11;
            return (r === 10 || r === 11) ? 0 : r;
        };

        return calc(9) === parseInt(clean.charAt(9), 10) && 
               calc(10) === parseInt(clean.charAt(10), 10);
    }

    function showError(input, msg) {
        if (!input) return;
        input.classList.add("input-error");
        const errSpan = document.getElementById(`err-${input.id}`);
        if (errSpan) errSpan.innerText = msg;
    }

    function clearErrors() {
        Object.values(fields).forEach((input) => {
            if (!input) return;
            input.classList.remove("input-error");
            const errSpan = document.getElementById(`err-${input.id}`);
            if (errSpan) errSpan.innerText = "";
        });
    }

    form.addEventListener("submit", (e) => {
        e.preventDefault();
        clearErrors();

        let isValid = true;

        if (fields.nome && !fields.nome.value.trim()) {
            showError(fields.nome, "Digite o nome completo.");
            isValid = false;
        }

        if (fields.cpf) {
            const cpfClean = fields.cpf.value.replace(/\D/g, "");

            if (!validarCPF(cpfClean)) {
                showError(fields.cpf, "Informe um CPF válido.");
                isValid = false;
            } else {
                const cpfsNaTabela = Array.from(document.querySelectorAll("table tbody tr td:nth-child(3)"))
                    .map(td => td.innerText.replace(/\D/g, ""));

                if (cpfsNaTabela.includes(cpfClean)) {
                    showError(fields.cpf, "Este CPF já possui um agendamento na lista.");
                    isValid = false;
                }
            }
        }

        if (fields.email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(fields.email.value.trim())) {
                showError(fields.email, "Informe um e-mail válido.");
                isValid = false;
            }
        }

        if (fields.especialidade && !fields.especialidade.value) {
            showError(fields.especialidade, "Selecione a especialidade.");
            isValid = false;
        }

        if (fields.data && !fields.data.value) {
            showError(fields.data, "Selecione a data.");
            isValid = false;
        }

        if (fields.hora && !fields.hora.value) {
            showError(fields.hora, "Selecione o horário.");
            isValid = false;
        }

        if (isValid) {
            form.submit();
        }
    });
});