<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['erro_login'] = 'Efetue o login para acessar a página.';
    header('Location: /meu-projeto-web/public/login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedConnect - Agendamentos e Pacientes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/meu-projeto-web/public/css/agendamentos.css">
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
                    <li><a href="#">📊 Dashboard</a></li>
                    <li><a href="#">📋 Prontuários</a></li>
                    <li><a href="/meu-projeto-web/public/agendamentos" class="active">📅 Agendamentos</a></li>
                </ul>
            </nav>
        </div>
        <div class="sidebar-footer">            
            <a href="/meu-projeto-web/public/logout" class="btn-logout">↳ Sair</a>
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
                <div class="user-avatar" title="<?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?>">
                    <?= strtoupper(substr($_SESSION['usuario_nome'] ?? 'U', 0, 2)); ?>
                </div>
            </div>
        </header>

        <?php if (!empty($_SESSION['mensagem_sucesso'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['mensagem_sucesso']; unset($_SESSION['mensagem_sucesso']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['mensagem_erro'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?>
            </div>
        <?php endif; ?>

        <section class="calendar-card">
            <div class="calendar-header">
                <span>Agosto de 2026</span>
                <div class="calendar-nav">
                    <button type="button" class="btn-nav-cal">‹</button>
                    <button type="button" class="btn-nav-cal">›</button>
                </div>
            </div>
            <div class="calendar-grid">
                <div class="day-name">Dom</div>
                <div class="day-name">Seg</div>
                <div class="day-name">Ter</div>
                <div class="day-name">Qua</div>
                <div class="day-name">Qui</div>
                <div class="day-name">Sex</div>
                <div class="day-name">Sab</div>

                <div class="day-cell">1</div>
                <div class="day-cell">2</div>
                <div class="day-cell">3</div>
                <div class="day-cell selected">4</div>
                <div class="day-cell has-appointment">5</div>
                <div class="day-cell">6</div>
                <div class="day-cell">7</div>
            </div>

            <div class="legend">
                <div class="legend-item"><span class="dot dot-selected"></span> Selecionado</div>
                <div class="legend-item"><span class="dot dot-today"></span> Hoje</div>
                <div class="legend-item"><span class="dot dot-appointment"></span> Com consultas</div>
            </div>
        </section>

        <section class="card">
            <h2 class="card-title">Adicionar Pacientes</h2>
            
            <form action="/meu-projeto-web/public/pacientes/salvar" method="POST" id="formPaciente">
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
                        <label>&nbsp;</label>
                        <button type="submit" class="btn-save">Salvar</button>
                    </div>
                </div>
            </form>
        </section>

        <section class="card">
            <h2 class="card-title">Listagem de Pacientes</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NOME</th>
                            <th>CPF</th>
                            <th>TELEFONE</th>
                            <th>E-MAIL</th>
                            <th>DATA</th>
                            <th>HORA</th>
                            <th class="actions-header">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pacientes)): ?>
                            <?php foreach ($pacientes as $paciente): ?>
                                <?php $isEditing = (isset($_GET['editar_id']) && $_GET['editar_id'] == $paciente['id']); ?>
                                
                                <?php if ($isEditing): ?>
                                    <tr>
                                        <td>
                                            <form action="/meu-projeto-web/public/pacientes/atualizar" method="POST" id="form-edit-<?= $paciente['id']; ?>">
                                                <input type="hidden" name="id" value="<?php echo $paciente['id']; ?>">
                                                <?php echo htmlspecialchars($paciente['id']); ?>
                                            </form>
                                        </td>
                                        <td><input type="text" form="form-edit-<?= $paciente['id']; ?>" name="nome" class="input-inline" value="<?php echo htmlspecialchars($paciente['nome']); ?>" required></td>
                                        <td><input type="text" form="form-edit-<?= $paciente['id']; ?>" name="cpf" class="input-inline" value="<?php echo htmlspecialchars($paciente['cpf']); ?>" required></td>
                                        <td><input type="text" form="form-edit-<?= $paciente['id']; ?>" name="telefone" class="input-inline" value="<?php echo htmlspecialchars($paciente['telefone'] ?? ''); ?>"></td>
                                        <td><input type="email" form="form-edit-<?= $paciente['id']; ?>" name="email" class="input-inline" value="<?php echo htmlspecialchars($paciente['email']); ?>" required></td>
                                        <td><?php echo htmlspecialchars($paciente['data_cadastro'] ?? '--/--/----'); ?></td>
                                        <td><?php echo htmlspecialchars($paciente['hora_cadastro'] ?? '--:--'); ?></td>
                                        <td class="actions-cell">
                                            <button type="submit" form="form-edit-<?= $paciente['id']; ?>" class="btn-action btn-save-inline" title="Salvar Alterações">✔</button>
                                            <a href="/meu-projeto-web/public/agendamentos" class="btn-action btn-delete" title="Cancelar">✖</a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($paciente['id']); ?></td>
                                        <td><?php echo htmlspecialchars($paciente['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($paciente['cpf']); ?></td>
                                        <td><?php echo htmlspecialchars($paciente['telefone'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($paciente['email']); ?></td>
                                        <td><?php echo htmlspecialchars($paciente['data_cadastro'] ?? '--/--/----'); ?></td>
                                        <td><?php echo htmlspecialchars($paciente['hora_cadastro'] ?? '--:--'); ?></td>
                                        <td class="actions-cell">
                                            <a href="/meu-projeto-web/public/agendamentos?editar_id=<?php echo $paciente['id']; ?>" class="btn-action btn-edit" title="Editar">✏️</a>
                                            <a href="/meu-projeto-web/public/pacientes/excluir?id=<?php echo $paciente['id']; ?>" class="btn-action btn-delete" title="Excluir" onclick="return confirm('Deseja realmente excluir este paciente?');">🗑️</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="empty-row">Nenhum paciente cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="/meu-projeto-web/public/js/agendamentos.js"></script>
</body>
</html>