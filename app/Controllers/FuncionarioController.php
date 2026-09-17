<?php

namespace App\Controllers;

use App\Models\Funcionario;

class FuncionarioController
{
    private function verificarAcessoAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_perfil'] ?? '') !== 'admin') {
            header('Location: /login');
            exit();
        }
    }

    public function cadastrar(): void
    {
        $this->verificarAcessoAdmin();

        $funcionarioModel = new Funcionario();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome   = trim($_POST['nome'] ?? '');
            $email  = trim($_POST['email'] ?? '');
            $perfil = trim($_POST['perfil'] ?? '');
            $senha  = trim($_POST['senha'] ?? '');

            if (!empty($nome) && !empty($email) && !empty($perfil) && !empty($senha)) {
                $usuarioExiste = $funcionarioModel->buscarPorEmail($email);

                if ($usuarioExiste) {
                    $_SESSION['mensagem_erro'] = 'Este e-mail já está cadastrado no sistema.';
                } elseif (strlen($senha) < 8) {
                    $_SESSION['mensagem_erro'] = 'A senha deve conter no mínimo 8 caracteres.';
                } else {
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    $sucesso = $funcionarioModel->inserir($nome, $email, $senhaHash, $perfil);

                    if ($sucesso) {
                        $_SESSION['mensagem_sucesso'] = 'Colaborador cadastrado com sucesso!';
                    } else {
                        $_SESSION['mensagem_erro'] = 'Erro ao cadastrar o colaborador no banco de dados.';
                    }
                }
            } else {
                $_SESSION['mensagem_erro'] = 'Preencha todos os campos do formulário.';
            }

            header('Location: /cadastrar');
            exit();
        }

        $colaboradores = $funcionarioModel->listarTodos();
        require_once __DIR__ . '/../Views/auth/cadastrar.php';
    }

    public function alterarStatus(): void
    {
        $this->verificarAcessoAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $novoStatus = $_POST['novo_status'] ?? 'inativo';

            if ($id === (int)$_SESSION['usuario_id']) {
                $_SESSION['mensagem_erro'] = 'Você não pode alterar o status do seu próprio usuário.';
            } else {
                $funcionarioModel = new Funcionario();
                $funcionarioModel->alterarStatus($id, $novoStatus);
                $_SESSION['mensagem_sucesso'] = 'Status do colaborador alterado com sucesso!';
            }
        }

        header('Location: /cadastrar');
        exit();
    }

    public function excluir(): void
    {
        $this->verificarAcessoAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);

            if ($id === (int)$_SESSION['usuario_id']) {
                $_SESSION['mensagem_erro'] = 'Você não pode excluir o seu próprio usuário.';
            } else {
                $funcionarioModel = new Funcionario();
                $funcionarioModel->excluir($id);
                $_SESSION['mensagem_sucesso'] = 'Colaborador excluído com sucesso!';
            }
        }

        header('Location: /cadastrar');
        exit();
    }
}
