<?php

namespace App\Controllers;

require_once __DIR__ . '/../Models/Paciente.php';

use App\Models\Paciente;

class PacienteController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        $model = new Paciente();
        $pacientes = $model->listar();

        require_once __DIR__ . '/../Views/agendamentos/index.php';
    }

    public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome     = trim($_POST['nome'] ?? '');
            $cpf      = trim($_POST['cpf'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $email    = trim($_POST['email'] ?? '');

            if (empty($nome) || empty($cpf) || empty($email)) {
                $_SESSION['mensagem_erro'] = "Preencha todos os campos obrigatórios!";
            } else {
                $model = new Paciente();
                if ($model->inserir($nome, $cpf, $telefone, $email)) {
                    $_SESSION['mensagem_sucesso'] = "Paciente cadastrado com sucesso!";
                } else {
                    $_SESSION['mensagem_erro'] = "Erro ao cadastrar paciente.";
                }
            }

            header('Location: /meu-projeto-web/public/agendamentos');
            exit;
        }
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $model = new Paciente();
            $pacienteEdicao = $model->buscarPorId($id);
            
            if ($pacienteEdicao) {
                $pacientes = $model->listar();
                require_once __DIR__ . '/../Views/agendamentos/index.php';
                return;
            } else {
                $_SESSION['mensagem_erro'] = "Paciente não encontrado!";
            }
        }

        header('Location: /meu-projeto-web/public/agendamentos');
        exit;
    }

    public function atualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id       = $_POST['id'] ?? null;
            $nome     = trim($_POST['nome'] ?? '');
            $cpf      = trim($_POST['cpf'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $email    = trim($_POST['email'] ?? '');

            if (!$id || empty($nome) || empty($cpf) || empty($email)) {
                $_SESSION['mensagem_erro'] = "Preencha todos os campos para atualizar!";
            } else {
                $model = new Paciente();
                if ($model->atualizar($id, $nome, $cpf, $telefone, $email)) {
                    $_SESSION['mensagem_sucesso'] = "Paciente atualizado com sucesso!";
                } else {
                    $_SESSION['mensagem_erro'] = "Erro ao atualizar registro.";
                }
            }

            header('Location: /meu-projeto-web/public/agendamentos');
            exit;
        }
    }

    public function excluir()
    {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $model = new Paciente();
            if ($model->excluir($id)) {
                $_SESSION['mensagem_sucesso'] = "Paciente removido com sucesso!";
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao excluir paciente.";
            }
        }

        header('Location: /meu-projeto-web/public/agendamentos');
        exit;
    }
}