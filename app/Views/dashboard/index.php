<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MedConnect</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8'); ?>/public/css/dashboard.css">
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="logo-icon" aria-hidden="true">M</div>
            <span>MedConnect</span>
        </div>
        <nav class="sidebar-nav">
            <ul class="sidebar-menu">
                <li><a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8'); ?>/dashboard" class="active">📊 Dashboard</a></li>
                <li><a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8'); ?>/prontuarios">📋 Prontuários</a></li>
                <li><a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8'); ?>/agendamentos">📅 Agendamentos</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8'); ?>/logout" class="btn-logout">↳ Sair</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>
                <h1>Painel Principal</h1>
                <p>Bem-vindo ao MedConnect</p>
            </div>
            <div class="user-avatar" title="<?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário', ENT_QUOTES, 'UTF-8'); ?>">
                <?= htmlspecialchars(strtoupper(substr($_SESSION['usuario_nome'] ?? 'U', 0, 2)), ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </header>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue" aria-hidden="true">📅</div>
                <div class="stat-info">
                    <span class="stat-title">Consultas Hoje</span>
                    <span class="stat-value"><?= htmlspecialchars((string)($stats['consultas_hoje'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green" aria-hidden="true">👥</div>
                <div class="stat-info">
                    <span class="stat-title">Pacientes Cadastrados</span>
                    <span class="stat-value"><?= htmlspecialchars((string)($stats['total_pacientes'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange" aria-hidden="true">📋</div>
                <div class="stat-info">
                    <span class="stat-title">Prontuários Atualizados</span>
                    <span class="stat-value"><?= htmlspecialchars((string)($stats['total_prontuarios'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple" aria-hidden="true">⏳</div>
                <div class="stat-info">
                    <span class="stat-title">Aguardando Atendimento</span>
                    <span class="stat-value"><?= htmlspecialchars((string)($stats['aguardando'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div> 
        </section>

        <section class="section-card">
            <h2>Ações Rápidas</h2>
            <div class="actions-grid">
                <div class="action-card" data-action="novo-prontuario" role="button" tabindex="0">
                    <div class="action-icon" aria-hidden="true">📋</div>
                    <div class="action-title">Novo Prontuário</div>
                    <div class="action-desc">Registrar atendimento clínico</div>
                </div>

                <div class="action-card" data-action="agendar-consulta" role="button" tabindex="0">
                    <div class="action-icon" aria-hidden="true">📅</div>
                    <div class="action-title">Agendar Consulta</div>
                    <div class="action-desc">Marcar horário para paciente</div>
                </div>

                <div class="action-card" data-action="cadastrar-paciente" role="button" tabindex="0">
                    <div class="action-icon" aria-hidden="true">👤</div>
                    <div class="action-title">Cadastrar Paciente</div>
                    <div class="action-desc">Adicionar novo cadastro de paciente</div>
                </div>
            </div>
        </section>

        <section class="section-card">
            <div class="section-header">
                <h2>Consultas Agendadas para Hoje</h2>
                <a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8'); ?>/agendamentos" class="link-see-all">Ver todas →</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Horário</th>
                        <th scope="col">Paciente</th>
                        <th scope="col">Médico / Especialidade</th>
                        <th scope="col">Status</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($consultasHoje)): ?>
                        <?php foreach ($consultasHoje as $consulta): ?>
                            <?php 
                                $horaConsulta = $consulta['hora'] ?? $consulta['hora_agendamento'] ?? null;
                                $pacienteId = urlencode((string)($consulta['paciente_id'] ?? ''));
                                $consultaId = urlencode((string)($consulta['id'] ?? ''));
                            ?>
                            <tr>
                                <td><?= !empty($horaConsulta) ? htmlspecialchars(date('H:i', strtotime($horaConsulta)), ENT_QUOTES, 'UTF-8') : '--:--'; ?></td>
                                <td><?= htmlspecialchars($consulta['paciente_nome'] ?? 'Paciente não informado', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?= htmlspecialchars($consulta['medico_nome'] ?? 'Médico Não Atribuído', ENT_QUOTES, 'UTF-8'); ?> 
                                    <small>(<?= htmlspecialchars($consulta['especialidade'] ?? 'Geral', ENT_QUOTES, 'UTF-8'); ?>)</small>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = match($consulta['status'] ?? '') {
                                        'Confirmado', 'Concluído' => 'status-confirmed',
                                        'Em Andamento', 'Em Atendimento' => 'status-in-progress',
                                        'Aguardando' => 'status-waiting',
                                        'Cancelado' => 'status-canceled',
                                        default => 'status-waiting'
                                    };
                                    ?>
                                    <span class="badge <?= $statusClass; ?>">
                                        <?= htmlspecialchars($consulta['status'] ?? 'Pendente', ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8'); ?>/prontuarios?paciente_id=<?= $pacienteId; ?>&consulta_id=<?= $consultaId; ?>" class="btn-table-action">
                                        Atender
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #64748b; padding: 24px;">
                                Nenhuma consulta agendada para o dia de hoje.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <script src="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8'); ?>/public/js/dashboard.js"></script>
</body>
</html>