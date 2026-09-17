document.addEventListener('DOMContentLoaded', () => {
    const actionRoutes = {
        'novo-prontuario': '/prontuarios',
        'agendar-consulta': '/agendamentos',
        'cadastrar-paciente': '/agendamentos#cadastrar'
    };

    document.querySelectorAll('.action-card').forEach(card => {
        card.addEventListener('click', () => {
            const action = card.getAttribute('data-action');
            if (actionRoutes[action]) {
                window.location.href = actionRoutes[action];
            }
        });
    });
});