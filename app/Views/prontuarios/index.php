<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prontuário Médico - MedConnect</title>
    <link rel="stylesheet" href="/public/css/prontuarios.css">
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="logo-icon">M</div>
            <span>MedConnect</span>
        </div>
        <nav class="sidebar-nav">
            <ul class="sidebar-menu">
                <li><a href="/dashboard">📊 Dashboard</a></li>
                <li><a href="/prontuarios" class="active">📋 Prontuários</a></li>
                <li><a href="/agendamentos">📅 Agendamentos</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="/logout" class="btn-logout">↳ Sair</a>
        </div>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div>
                <h1>Prontuário Médico</h1>
                <p>Registro clínico do paciente</p>
            </div>
            <div class="topbar-actions" style="display: flex; align-items: center; gap: 15px;">
                <form method="GET" action="/prontuarios" class="search-box">
                    <?php $termoBusca = $_GET['busca'] ?? $_GET['paciente_id'] ?? ''; ?>
                    <input type="text" name="busca" placeholder="Buscar por ID ou CPF..." value="<?= htmlspecialchars($termoBusca, ENT_QUOTES, 'UTF-8'); ?>">
                </form>
                
                <button class="notification-btn" type="button" aria-label="Notificações">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notification-badge"></span>
                </button>
                
                <div class="user-avatar" title="<?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário', ENT_QUOTES, 'UTF-8'); ?>">
                    <?= strtoupper(substr($_SESSION['usuario_nome'] ?? 'U', 0, 2)); ?>
                </div>
            </div>
        </header>

        <?php if (!empty($paciente)): ?>
            <form id="formProntuario" action="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8'); ?>/prontuarios/salvar" method="POST" enctype="multipart/form-data">
                
                <input type="hidden" name="paciente_id" value="<?= htmlspecialchars($paciente['id'] ?? $_GET['paciente_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="prontuario_id" value="<?= htmlspecialchars($prontuario['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="consulta_id" value="<?= htmlspecialchars($_GET['consulta_id'] ?? $consulta['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="idade" value="<?= htmlspecialchars($paciente['idade'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                <section class="section-card">
                    <div class="patient-header-card">
                        <div class="patient-avatar-title">
                            <div class="avatar-circle">
                                <?= strtoupper(substr($paciente['nome'] ?? 'NA', 0, 2)); ?>
                            </div>
                            <div>
                                <h2><?= htmlspecialchars($paciente['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
                                <span>CPF: <?= htmlspecialchars($paciente['cpf'] ?? '---', ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        </div>

                        <div class="patient-info-grid">
                            <div>
                                <div class="info-label">Idade</div>
                                <div class="info-value">
                                    <?= isset($paciente['idade']) && $paciente['idade'] !== null ? htmlspecialchars($paciente['idade'], ENT_QUOTES, 'UTF-8') . ' anos' : '---'; ?>
                                </div>
                            </div>
                            <div>
                                <div class="info-label">Tipo Sanguíneo</div>
                                <div class="info-value">
                                    <select name="tipo_sanguineo" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 8px; font-weight: 600; color: #0f172a; outline: none; background: #ffffff;">
                                        <option value="">-- Selecione --</option>
                                        <?php 
                                        $tipos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                        $tsAtual = $paciente['tipo_sanguineo'] ?? '';
                                        foreach ($tipos as $ts): 
                                        ?>
                                            <option value="<?= $ts; ?>" <?= $tsAtual === $ts ? 'selected' : ''; ?>><?= $ts; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <div class="info-label">Convênio</div>
                                <div class="info-value"><?= htmlspecialchars($paciente['convenio'] ?? 'Não informado', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                            <div>
                                <div class="info-label">Última Consulta</div>
                                <div class="info-value">
                                    <?= !empty($paciente['ultima_consulta']) ? date('d/m/Y', strtotime($paciente['ultima_consulta'])) : '--/--/----'; ?>
                                </div>
                            </div>
                        </div>

                        <?php 
                        $alergias = [];
                        if (!empty($paciente['alergias'])) {
                            $alergias = is_array($paciente['alergias']) 
                                ? $paciente['alergias'] 
                                : array_map('trim', explode(',', $paciente['alergias']));
                        }
                        ?>
                        <?php if (!empty($alergias)): ?>
                            <div style="margin-top: 16px;">
                                <div class="info-label">Alertas Médicos / Alergias</div>
                                <div class="allergies-list">
                                    <?php foreach ($alergias as $alergia): ?>
                                        <span class="tag-alert">⚠️ <?= htmlspecialchars($alergia, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="section-card">
                    <div class="grid-form-2col">
                        <div class="form-group-card">
                            <h3>Diagnóstico e Evolução Clínica</h3>
                            <textarea name="diagnostico" class="form-control-textarea" placeholder="Descreva a evolução do paciente..."><?= htmlspecialchars($prontuario['diagnostico'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        <div class="form-group-card">
                            <h3>Prescrição Médica</h3>
                            <textarea name="prescricao" class="form-control-textarea" placeholder="Descreva os medicamentos e orientações..."><?= htmlspecialchars($prontuario['prescricao'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group-card">
                        <h3>Anexos e Exames</h3>
                        <div class="upload-drop-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
                            <p>Arraste e solte os exames aqui</p>
                            <p>ou clique para selecionar arquivos (PDF, PNG, JPG)</p>
                            <input type="file" name="anexos[]" id="fileInput" multiple style="display:none;">
                        </div>
                        <div class="file-list" id="fileList"></div>

                        <?php if (!empty($anexos)): ?>
                            <div style="margin-top: 20px;">
                                <div class="info-label" style="margin-bottom: 8px;">Anexos Salvos</div>
                                <div class="file-list">
                                    <?php foreach ($anexos as $anexo): ?>
                                        <?php 
                                            $caminhoNormalizado = str_replace('\\', '/', $anexo['caminho']);
                                            $nomeArquivo = basename($caminhoNormalizado);
                                            $urlDownload = htmlspecialchars(($basePath ?? '') . '/public/uploads/exames/' . $nomeArquivo, ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <div class="file-item">
                                            <span>📄 <?= htmlspecialchars($anexo['nome_original'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <a href="<?= $urlDownload; ?>" target="_blank" rel="noopener noreferrer" style="color: #0284c7; font-weight: 600; text-decoration: none; font-size: 0.85rem;">
                                                Visualizar / Download
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions-bottom">
                        <a href="<?= htmlspecialchars(($basePath ?? '') . '/prontuarios', ENT_QUOTES, 'UTF-8'); ?>" id="btnCancelar" class="btn-secondary" style="text-decoration: none; display: inline-block; text-align: center;">Cancelar</a>
                        <button type="submit" id="btnSalvar" class="btn-primary">Salvar Prontuário</button>
                    </div>
                </section>
            </form>

        <?php else: ?>
            <section class="section-card" style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 3rem; margin-bottom: 12px;">🔍</div>
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Nenhum paciente selecionado</h2>
                <p style="color: #64748b; font-size: 0.95rem;">Digite o ID ou CPF do paciente na barra de pesquisa acima e pressione Enter para carregar o prontuário.</p>
            </section>
        <?php endif; ?>

    </main>

    <script src="/public/js/prontuarios.js"></script>

</body>
</html>