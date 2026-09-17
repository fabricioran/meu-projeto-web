<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gera um token CSRF seguro caso ainda não exista na sessão
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedConnect - Gestão de Colaboradores</title>
    <link rel="stylesheet" href="/public/css/cadastrar.css">
</head>
<body>

    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="logo-icon">M</div>
                <h2>MedConnect</h2>
            </div>

            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    <?php if (($_SESSION['usuario_perfil'] ?? '') === 'admin'): ?>
                        <li>
                            <a href="/cadastrar" class="nav-item active">
                                <span>👥</span>
                                <span>Gestão de Colaboradores</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="/dashboard" class="nav-item">
                                <span>📊</span>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="/agendamentos" class="nav-item">
                                <span>📅</span>
                                <span>Agendamentos</span>
                            </a>
                        </li>
                        <?php if (($_SESSION['usuario_perfil'] ?? '') !== 'recepcao'): ?>
                            <li>
                                <a href="/prontuarios" class="nav-item">
                                    <span>📋</span>
                                    <span>Prontuários</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="/logout" class="btn-logout">Sair do Sistema</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="content-header">
                <div>
                    <h1>Gestão de Colaboradores</h1>
                    <p>Cadastre novos usuários e controle os acessos ao sistema</p>
                </div>
                <a href="/dashboard" class="btn-voltar-painel">&larr; Voltar ao Painel</a>
            </header>

            <?php if (isset($_SESSION['mensagem_erro'])): ?>
                <div class="alert alert-error"><?= htmlspecialchars($_SESSION['mensagem_erro'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php unset($_SESSION['mensagem_erro']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_SESSION['mensagem_sucesso'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php unset($_SESSION['mensagem_sucesso']); ?>
            <?php endif; ?>

            <div class="grid-container">
                <div class="card-box">
                    <h3>Cadastrar Novo Colaborador</h3>
                    <form method="POST" action="/cadastrar">
                        <!-- Token CSRF para prevenção contra ataques cross-site -->
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                        <div class="form-group">
                            <label>NOME COMPLETO</label>
                            <input type="text" name="nome" placeholder="Digite o nome completo" required>
                        </div>

                        <div class="form-group">
                            <label>E-MAIL PROFISSIONAL</label>
                            <input type="email" name="email" placeholder="seuemail@medconnect.com" required>
                        </div>

                        <div class="form-group">
                            <label>PERFIL DE ACESSO (ROLE)</label>
                            <select name="perfil" id="selectPerfil" required>
                                <option value="" disabled selected>Selecione a permissão...</option>
                                <option value="medico">Médico (Doutor)</option>
                                <option value="recepcao">Recepção (Secretária)</option>
                                <option value="admin">Administrador (Acesso Total)</option>
                            </select>
                        </div>

                        <!-- Campo condicional ativado ao selecionar Médico -->
                        <div class="form-group" id="grupoCrm" style="display: none;">
                            <label>CRM / REGISTRO PROFISSIONAL</label>
                            <input type="text" name="crm" id="inputCrm" placeholder="Ex: CRM/PE 123456">
                        </div>

                        <div class="form-group">
                            <label>SENHA PROVISÓRIA</label>
                            <input type="password" name="senha" placeholder="Mínimo 8 caracteres" required minlength="8">
                        </div>

                        <button type="submit" class="btn-submit">Cadastrar Colaborador</button>
                    </form>
                </div>

                <div class="card-box">
                    <h3>Colaboradores Cadastrados</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Perfil</th>
                                    <th>Status</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php if (!empty($colaboradores) && is_array($colaboradores)): ?>
                                <?php foreach ($colaboradores as $colab): ?>
                                    <?php 
                                        $estaAtivo = (int)($colab['ativo'] ?? 1) === 1;
                                        $idColab = (int)($colab['id'] ?? 0);
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($colab['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong><br>
                                            <small style="color: #718096;"><?= htmlspecialchars($colab['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= htmlspecialchars($colab['perfil'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                                <?= ucfirst(htmlspecialchars($colab['perfil'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-tag <?= $estaAtivo ? 'status-ativo' : 'status-inativo' ?>">
                                                <?= $estaAtivo ? 'Ativo' : 'Inativo' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($idColab !== (int)($_SESSION['usuario_id'] ?? 0)): ?>
                                                <div style="display: flex; gap: 6px; align-items: center;">
                                                    <form method="POST" action="/funcionarios/status" style="margin:0;" onsubmit="return confirm('Deseja realmente alterar o status deste colaborador?');">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                        <input type="hidden" name="id" value="<?= $idColab ?>">
                                                        <input type="hidden" name="novo_status" value="<?= $estaAtivo ? 'inativo' : 'ativo' ?>">
                                                        <button type="submit" class="btn-action <?= $estaAtivo ? 'btn-desativar' : 'btn-ativar' ?>">
                                                            <?= $estaAtivo ? 'Desativar Acesso' : 'Ativar Acesso' ?>
                                                        </button>
                                                    </form>

                                                    <form method="POST" action="/funcionarios/excluir" style="margin:0;" onsubmit="return confirm('Tem certeza que deseja excluir este colaborador permanentemente?');">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                        <input type="hidden" name="id" value="<?= $idColab ?>">
                                                        <button type="submit" class="btn-action btn-excluir">
                                                            Excluir
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php else: ?>
                                                <small style="color: #a0aec0; font-weight: 600;">Você</small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #718096;">Nenhum colaborador encontrado.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Exibe/oculta o campo de CRM caso a função selecionada seja Médico
        document.getElementById('selectPerfil').addEventListener('change', function() {
            const grupoCrm = document.getElementById('grupoCrm');
            const inputCrm = document.getElementById('inputCrm');
            if (this.value === 'medico') {
                grupoCrm.style.display = 'block';
                inputCrm.setAttribute('required', 'required');
            } else {
                grupoCrm.style.display = 'none';
                inputCrm.removeAttribute('required');
                inputCrm.value = '';
            }
        });
    </script>
</body>
</html>