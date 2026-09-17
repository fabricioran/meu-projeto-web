<?php

namespace App\Controllers;

use App\Models\Funcionario;

class AuthController
{
    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['usuario_id'])) {
            $this->redirecionarPorPerfil($_SESSION['usuario_perfil'] ?? '');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
            $senha = trim($_POST['senha'] ?? '');

            if (!$email) {
                $_SESSION['erro_login'] = 'Informe um endereço de e-mail válido.';
                header('Location: /login');
                exit();
            }

            if (empty($senha) || strlen($senha) < 8) {
                $_SESSION['erro_login'] = 'A senha deve conter no mínimo 8 caracteres.';
                header('Location: /login');
                exit();
            }

            $funcionarioModel = new Funcionario();
            $usuario = $funcionarioModel->buscarPorEmail($email);

            if ($usuario && isset($usuario['ativo']) && (int)$usuario['ativo'] === 0) {
                $_SESSION['erro_login'] = 'Sua conta está desativada. Fale com o Administrador.';
                header('Location: /login');
                exit();
            }

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION = [];
                if (ini_get('session.use_cookies')) {
                    $params = session_get_cookie_params();
                    setcookie(
                        session_name(),
                        '',
                        time() - 42000,
                        $params['path'],
                        $params['domain'],
                        $params['secure'],
                        $params['httponly']
                    );
                }
                session_destroy();

                session_start();
                session_regenerate_id(true);

                $_SESSION['session_token']   = bin2hex(random_bytes(32));
                $_SESSION['usuario_id']      = $usuario['id'];
                $_SESSION['usuario_nome']    = $usuario['nome'];
                $_SESSION['usuario_perfil']  = strtolower(trim($usuario['perfil']));
                $_SESSION['login_timestamp'] = time();

                $this->redirecionarPorPerfil($_SESSION['usuario_perfil']);
            } else {
                $_SESSION['erro_login'] = 'E-mail ou senha inválidos.';
            }

            header('Location: /login');
            exit();
        }

        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        header('Location: /login');
        exit();
    }

    private function redirecionarPorPerfil(string $perfil): void
    {
        switch (strtolower(trim($perfil))) {
            case 'medico':
                header('Location: /prontuarios');
                break;
            case 'recepcao':
            case 'recepcionista':
                header('Location: /agendamentos');
                break;
            case 'admin':
                header('Location: /cadastrar');
                break;
            default:
                header('Location: /dashboard');
                break;
        }
        exit();
    }
}
