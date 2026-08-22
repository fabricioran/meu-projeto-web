<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedConnect - Cadastro</title>
    <link rel="stylesheet" href="/meu-projeto-web/public/css/login.css">
</head>
<body>

    <!-- Link correto para voltar ao login -->
    <a href="/meu-projeto-web/public/login" class="top-link">&lt; Voltar para Login</a>

    <div class="card">
        <div class="logo-icon">M</div>
        <h1 class="title">Criar Conta</h1>
        <p class="subtitle">Cadastre-se no MedConnect</p>

        <!-- Correção: só exibe erro se não estiver vazio -->
        <?php if (!empty($_SESSION['mensagem_erro'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?></div>
        <?php endif; ?>

        <!-- Exibe mensagem de sucesso se cadastro foi concluído -->
        <?php if (!empty($_SESSION['mensagem_sucesso'])): ?>
            <div class="alert alert-success"><?= $_SESSION['mensagem_sucesso']; unset($_SESSION['mensagem_sucesso']); ?></div>
        <?php endif; ?>

        <form action="/meu-projeto-web/public/cadastrar" method="POST" id="formCadastro">
            <div class="form-group">
                <label for="nome">NOME COMPLETO</label>
                <input type="text" id="nome" name="nome" required placeholder="Digite seu nome">
            </div>

            <div class="form-group">
                <label for="email">E-MAIL</label>
                <input type="email" id="email" name="email" required placeholder="seuemail@exemplo.com">
            </div>

            <div class="form-group">
                <label for="senha">SENHA</label>
                <input type="password" id="senha" name="senha" required placeholder="••••••">
            </div>

            <button type="submit" class="btn-submit">Finalizar Cadastro</button>
        </form>

