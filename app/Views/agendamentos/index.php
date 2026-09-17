<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedConnect - Agendamentos e Pacientes</title>    
    <link rel="stylesheet" href="/public/css/agendamentos.css?v=<?= time(); ?>">
</head>
<body>

    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <div class="logo-icon">M</div>
                <span>MedConnect</span>
            </div>
            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    <li><a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8') ?>/dashboard">📊 Dashboard</a></li>
                    <li><a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8') ?>/prontuarios">📋 Prontuários</a></li>
                    <li><a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8') ?>/agendamentos" class="active">📅 Agendamentos</a></li>
                </ul>
            </nav>
        </div>
        <div class="sidebar-footer">            
            <a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8') ?>/logout" class="btn-logout">↳ Sair</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-title">
                <h1>Agendamentos</h1>
                <p>Gestão de consultas e calendário</p>
            </div>
            <div class="topbar-actions">
                <div class="search-box">
                    <input type="text" placeholder="Buscar...">
                </div>
                
                <button class="notification-btn" type="button" aria-label="Notificações">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notification-badge"></span>
                </button>
                
                <div class="user-avatar" title="<?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário', ENT_QUOTES, 'UTF-8'); ?>">
                    <?= htmlspecialchars(strtoupper(substr($_SESSION['usuario_nome'] ?? 'U', 0, 2)), ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
        </header>

        <?php if (!empty($_SESSION['mensagem_sucesso'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['mensagem_sucesso'], ENT_QUOTES, 'UTF-8'); ?>
                <?php unset($_SESSION['mensagem_sucesso']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['mensagem_erro'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['mensagem_erro'], ENT_QUOTES, 'UTF-8'); ?>
                <?php unset($_SESSION['mensagem_erro']); ?>
            </div>
        <?php endif; ?>

        <section class="calendar-card">
            <div class="calendar-header">
                <span><?= htmlspecialchars(ucfirst($nomeMes ?? '') . ' de ' . ($anoCalendario ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                <div class="calendar-nav">
                    <a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8') ?>/agendamentos?mes=<?= urlencode($mesAnterior ?? ''); ?>&ano=<?= urlencode($anoAnterior ?? ''); ?>" class="btn-nav-cal">‹</a>
                    <a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8') ?>/agendamentos?mes=<?= urlencode($mesSeguinte ?? ''); ?>&ano=<?= urlencode($anoSeguinte ?? ''); ?>" class="btn-nav-cal">›</a>
                </div>
            </div>

            <div class="calendar-grid">
                <div class="day-name">Dom</div>
                <div class="day-name">Seg</div>
                <div class="day-name">Ter</div>
                <div class="day-name">Qua</div>
                <div class="day-name">Qui</div>
                <div class="day-name">Sex</div>
                <div class="day-name">Sáb</div>

                <?php for ($i = 0; $i < ($primeiroDiaSemana ?? 0); $i++): ?>
                    <div class="day-cell empty"></div>
                <?php endfor; ?>

                <?php for ($dia = 1; $dia <= ($totalDiasMes ?? 0); $dia++): ?>
                    <?php 
                        $classes = ['day-cell'];
                        
                        if (($hojeDia ?? null) !== null && $dia === $hojeDia) {
                            $classes[] = 'today-highlight';
                        }
                        
                        if (in_array($dia, $diasComConsulta ?? [], true)) {
                            $classes[] = 'has-appointment';
                        }
                    ?>
                    <div class="<?= implode(' ', $classes); ?>">
                        <span class="day-number"><?= $dia; ?></span>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="legend">
                <div class="legend-item"><span class="dot dot-today"></span> Hoje</div>
                <div class="legend-item"><span class="dot dot-appointment"></span> Com consultas</div>
            </div>
        </section>

        <section class="card">
            <h2 class="card-title">Adicionar Pacientes e Agendamento</h2>
            
            <form action="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8') ?>/agendamentos/salvar" method="POST" id="formPaciente">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" placeholder="Digite o nome completo" required>
                        <span class="error-msg" id="err-nome"></span>
                    </div>

                    <div class="form-group">
                        <label for="cpf">CPF:</label>
                        <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required>
                        <span class="error-msg" id="err-cpf"></span>
                    </div>

                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="text" id="telefone" name="telefone" placeholder="(00) 00000-0000" maxlength="15">
                        <span class="error-msg" id="err-telefone"></span>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" placeholder="usuario@email.com" required>
                        <span class="error-msg" id="err-email"></span>
                    </div>

                    <div class="form-group">
                        <label for="especialidade">Especialidade:</label>
                        <select id="especialidade" name="especialidade" required>
                            <option value="">Selecione...</option>
                            <?php foreach (($especialidadesDisponiveis ?? []) as $esp): ?>
                                <option value="<?= htmlspecialchars($esp, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($esp, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error-msg" id="err-especialidade"></span>
                    </div>

                    <div class="form-group">
                        <label for="data_agendamento">Data:</label>
                        <input type="date" id="data_agendamento" name="data_agendamento" required>
                        <span class="error-msg" id="err-data"></span>
                    </div>

                    <div class="form-group">
                        <label for="hora_agendamento">Hora:</label>
                        <input type="time" id="hora_agendamento" name="hora_agendamento" required>
                        <span class="error-msg" id="err-hora"></span>
                    </div>

                    <div class="form-group btn-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn-save">Salvar</button>
                    </div>
                </div>
            </form>
        </section>

        <section class="card">
            <h2 class="card-title">Listagem de Agendamentos</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NOME</th>
                            <th>CPF</th>
                            <th>TELEFONE</th>
                            <th>E-MAIL</th>
                            <th>ESPECIALIDADE</th>
                            <th>DATA</th>
                            <th>HORA</th>
                            <th class="actions-header">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($agendamentos) && is_array($agendamentos)): ?>
                            <?php foreach ($agendamentos as $index => $agendamento): ?>
                                <?php 
                                    $editarIdParam = filter_input(INPUT_GET, 'editar_id', FILTER_VALIDATE_INT);
                                    
                                    $agendamentoId = (int)($agendamento['agendamento_id'] ?? $agendamento['id'] ?? 0);
                                    $isEditing = ($editarIdParam !== false && $editarIdParam === $agendamentoId); 

                                    $dataRaw = $agendamento['data_agendamento'] ?? $agendamento['data'] ?? '';
                                    $horaRaw = $agendamento['hora_agendamento'] ?? $agendamento['hora'] ?? '';
                                    
                                    $dataInputValue = !empty($dataRaw) ? date('Y-m-d', strtotime($dataRaw)) : '';
                                    $dataExibir = !empty($dataRaw) ? date('d/m/Y', strtotime($dataRaw)) : '';
                                    $horaExibir = !empty($horaRaw) ? date('H:i', strtotime($horaRaw)) : '';
                                    
                                    $especialidadeExibir = $agendamento['especialidade'] ?? '';
                                    $idExibicao = $index + 1;
                                ?>
                                
                                <?php if ($isEditing): ?>
                                    <tr>
                                        <td colspan="9" style="padding: 0;">
                                            <form action="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8') ?>/agendamentos/atualizar" method="POST">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                                <input type="hidden" name="id" value="<?= $agendamentoId; ?>">
                                                <input type="hidden" name="paciente_id" value="<?= htmlspecialchars($agendamento['paciente_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                                                <table style="width: 100%; border-collapse: collapse;">
                                                    <tr>
                                                        <td style="width: 5%;"><?= $idExibicao; ?></td>
                                                        <td><input type="text" name="nome" class="input-inline" value="<?= htmlspecialchars($agendamento['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required></td>
                                                        <td><input type="text" name="cpf" class="input-inline" value="<?= htmlspecialchars($agendamento['cpf'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required></td>
                                                        <td><input type="text" name="telefone" class="input-inline" value="<?= htmlspecialchars($agendamento['telefone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                                                        <td><input type="email" name="email" class="input-inline" value="<?= htmlspecialchars($agendamento['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required></td>
                                                        <td>
                                                            <select name="especialidade" class="input-inline" required>
                                                                <option value="">Selecione...</option>
                                                                <?php foreach (($especialidadesDisponiveis ?? []) as $esp): ?>
                                                                    <option value="<?= htmlspecialchars($esp, ENT_QUOTES, 'UTF-8'); ?>" <?= ($especialidadeExibir === $esp) ? 'selected' : ''; ?>>
                                                                        <?= htmlspecialchars($esp, ENT_QUOTES, 'UTF-8'); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>
                                                        <td><input type="date" name="data_agendamento" class="input-inline" value="<?= htmlspecialchars($dataInputValue, ENT_QUOTES, 'UTF-8'); ?>" required></td>
                                                        <td><input type="time" name="hora_agendamento" class="input-inline" value="<?= htmlspecialchars($horaRaw, ENT_QUOTES, 'UTF-8'); ?>" required></td>
                                                        <td class="actions-cell">
                                                            <button type="submit" class="btn-action btn-save-inline" title="Salvar Alterações">✓</button>
                                                            <a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8') ?>/agendamentos" class="btn-action btn-delete" title="Cancelar">✕</a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </form>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td><?= $idExibicao; ?></td>
                                        <td><?= htmlspecialchars($agendamento['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($agendamento['cpf'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($agendamento['telefone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($agendamento['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($especialidadeExibir, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($dataExibir, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($horaExibir, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="actions-cell">
                                            <a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8') ?>/agendamentos?editar_id=<?= $agendamentoId; ?>" class="btn-action btn-edit" title="Editar">✏️</a>

                                            <form action="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8') ?>/agendamentos/excluir" method="POST" style="display: inline;" onsubmit="return confirm('Deseja realmente excluir este agendamento?');">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                                <input type="hidden" name="id" value="<?= $agendamentoId; ?>">
                                                <button type="submit" class="btn-action btn-delete" title="Excluir" style="background: none; border: none; cursor: pointer; padding: 0;">🗑️</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="empty-row">Nenhum agendamento cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="/public/js/agendamentos.js"></script>
</body>
</html>