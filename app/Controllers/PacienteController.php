<?php

namespace App\Controllers;

use App\Models\Paciente;

class PacienteController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index(): void
    {
        $model = new Paciente();
        $pacientes = $model->listar();

        require_once __DIR__ . '/../Views/agendamentos/index.php';
    }

    public function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome     = trim($_POST['nome'] ?? '');
            $cpf      = trim($_POST['cpf'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

            if (empty($nome) || empty($cpf) || !$email) {
                $_SESSION['mensagem_erro'] = 'Preencha todos os campos obrigatórios com dados válidos!';
            } else {
                $model = new Paciente();
                if ($model->inserir($nome, $cpf, $telefone, $email)) {
                    $_SESSION['mensagem_sucesso'] = 'Paciente cadastrado com sucesso!';
                } else {
                    $_SESSION['mensagem_erro'] = 'Erro ao cadastrar paciente.';
                }
            }

            header('Location: /agendamentos');
            exit();
        }
    }

    public function editar(): void
    {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $model = new Paciente();
            $pacienteEdicao = $model->buscarPorId($id);

            if ($pacienteEdicao) {
                $pacientes = $model->listar();
                require_once __DIR__ . '/../Views/agendamentos/index.php';
                return;
            }
            $_SESSION['mensagem_erro'] = 'Paciente não encontrado!';
        }

        header('Location: /agendamentos');
        exit();
    }

    public function atualizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id       = $_POST['id'] ?? null;
            $nome     = trim($_POST['nome'] ?? '');
            $cpf      = trim($_POST['cpf'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

            if (!$id || empty($nome) || empty($cpf) || !$email) {
                $_SESSION['mensagem_erro'] = 'Preencha todos os campos corretamente para atualizar!';
            } else {
                $model = new Paciente();
                if ($model->atualizar($id, $nome, $cpf, $telefone, $email)) {
                    $_SESSION['mensagem_sucesso'] = 'Paciente atualizado com sucesso!';
                } else {
                    $_SESSION['mensagem_erro'] = 'Erro ao atualizar registro.';
                }
            }

            header('Location: /agendamentos');
            exit();
        }
    }

    public function excluir(): void
    {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $model = new Paciente();
            if ($model->excluir($id)) {
                $_SESSION['mensagem_sucesso'] = 'Paciente removido com sucesso!';
            } else {
                $_SESSION['mensagem_erro'] = 'Erro ao excluir paciente.';
            }
        }

        header('Location: /agendamentos');
        exit();
    }
}
