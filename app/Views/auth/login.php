<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedConnect - Login</title>
    <link rel="stylesheet" href="/public/css/login.css">
</head>
<body>

    <a href="/home" class="top-link">&lt; Voltar ao Portal Público</a>

    <div class="card">
        <div class="logo-icon">M</div>
        <h1 class="title">MedConnect</h1>
        <p class="subtitle">Área restrita - Profissionais Autorizados</p>

        <?php if (!empty($_SESSION['erro_login'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['erro_login']); unset($_SESSION['erro_login']); ?></div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['mensagem_sucesso'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['mensagem_sucesso']); unset($_SESSION['mensagem_sucesso']); ?></div>
        <?php endif; ?>

        <form action="/login" method="POST" id="formLogin">
            <div class="form-group">
                <label for="email">E-MAIL</label>
                <input type="email" id="email" name="email" required placeholder="Digite o seu e-mail">
            </div>

            <div class="form-group">
                <div class="label-row">
                    <label for="senha">SENHA</label>
                    <a href="#" class="forgot-link">Esqueceu?</a>
                </div>
                <input type="password" id="senha" name="senha" required placeholder="Digite a senha de acesso">
            </div>

            <div class="checkbox-row">
                <input type="checkbox" id="lembrar" name="lembrar">
                <label for="lembrar" style="margin:0; font-weight:normal;">Lembrar de mim</label>
            </div>

            <button type="submit" class="btn-submit">Entrar</button>
        </form>
    </div>

    <script src="/public/js/login.js"></script>
</body>
</html>
