<?php

namespace App\Controllers;

use App\Models\Funcionario;

class AuthController
{
    public function cadastrar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome  = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = trim($_POST['senha'] ?? '');

            if (!empty($nome) && !empty($email) && !empty($senha)) {
                $funcionarioModel = new Funcionario();

                if ($funcionarioModel->buscarPorEmail($email)) {
                    $_SESSION['mensagem_erro'] = 'E-mail já cadastrado!';
                    header('Location: /meu-projeto-web/public/cadastrar');
                    exit;
                }

                $funcionarioModel->inserir($nome, $email, $senha, 'usuario');

                $_SESSION['mensagem_sucesso'] = 'Conta criada com sucesso! Faça login para acessar.';
                header('Location: /meu-projeto-web/public/login');
                exit;
            } else {
                $_SESSION['mensagem_erro'] = 'Preencha todos os campos obrigatórios.';
                header('Location: /meu-projeto-web/public/cadastrar');
                exit;
            }
        }

        require_once __DIR__ . '/../Views/auth/cadastrar.php'; // ← ajuste
    }

    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $senha = trim($_POST['senha'] ?? '');

            if (!empty($email) && !empty($senha)) {
                $funcionarioModel = new Funcionario();
                $usuario = $funcionarioModel->buscarPorEmail($email);

                if ($usuario && password_verify($senha, $usuario['senha'])) {
                    $_SESSION['usuario_id']     = $usuario['id'];
                    $_SESSION['usuario_nome']   = $usuario['nome'];
                    $_SESSION['usuario_perfil'] = $usuario['perfil'];

                    header('Location: /meu-projeto-web/public/agendamentos');
                    exit;
                } else {
                    $_SESSION['mensagem_erro'] = 'E-mail ou senha inválidos.';
                }
            } else {
                $_SESSION['mensagem_erro'] = 'Preencha todos os campos.';
            }

            header('Location: /meu-projeto-web/public/login');
            exit;
        }

        require_once __DIR__ . '/../Views/auth/login.php'; 
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: /meu-projeto-web/public/home');
        exit;
    }
}
