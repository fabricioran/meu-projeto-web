<?php

namespace App\Controllers;

use App\Models\Agendamentos;
use Config\Database;
use IntlDateFormatter;

class AgendamentosController
{
    private Agendamentos $model;
    private string $basePath;

    public function __construct($pdo = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $this->basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['erro_login'] = 'Efetue o login para acessar a página.';
            header('Location: ' . $this->basePath . '/login');
            exit();
        }

        $pdo = $pdo ?? Database::getConnection();
        $this->model = new Agendamentos($pdo);
    }

    public function index(): void
    {
        date_default_timezone_set('America/Sao_Paulo');

        $anoCalendario = isset($_GET['ano']) ? (int)$_GET['ano'] : (int)date('Y');
        $mesCalendario = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');

        if ($mesCalendario < 1) {
            $mesCalendario = 12;
            $anoCalendario--;
        } elseif ($mesCalendario > 12) {
            $mesCalendario = 1;
            $anoCalendario++;
        }

        $mesAnterior = $mesCalendario - 1;
        $anoAnterior = $anoCalendario;
        if ($mesAnterior < 1) {
            $mesAnterior = 12;
            $anoAnterior--;
        }

        $mesSeguinte = $mesCalendario + 1;
        $anoSeguinte = $anoCalendario;
        if ($mesSeguinte > 12) {
            $mesSeguinte = 1;
            $anoSeguinte++;
        }

        // Formatação do nome do mês
        if (class_exists('IntlDateFormatter')) {
            $formatter = new IntlDateFormatter('pt_BR', IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'America/Sao_Paulo', null, 'MMMM');
            $nomeMes = $formatter->format(mktime(0, 0, 0, $mesCalendario, 1, $anoCalendario));
        } else {
            $meses = [1 => 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
            $nomeMes = $meses[$mesCalendario] ?? '';
        }

        $hojeDia = null;
        if (date('Y-m') === sprintf('%04d-%02d', $anoCalendario, $mesCalendario)) {
            $hojeDia = (int)date('d');
        }

        $primeiroDiaSemana = (int)date('w', strtotime("{$anoCalendario}-{$mesCalendario}-01"));
        $totalDiasMes = (int)date('t', strtotime("{$anoCalendario}-{$mesCalendario}-01"));

      
        $agendamentos = $this->model->listarTodos();

        $diasComConsulta = [];
        if (!empty($agendamentos)) {
            foreach ($agendamentos as $ag) {
                $dataAg = $ag['data_agendamento'] ?? $ag['data'] ?? '';
                if (!empty($dataAg)) {
                    $dataPartes = explode('-', $dataAg);
                    if (count($dataPartes) === 3 && (int)$dataPartes[0] === $anoCalendario && (int)$dataPartes[1] === $mesCalendario) {
                        $diasComConsulta[] = (int)$dataPartes[2];
                    }
                }
            }
        }

        $especialidadesDisponiveis = [
            'Cardiologia',
            'Pediatria',
            'Neurologia',
            'Ortopedia',
            'Oftalmologia',
            'Clínica Geral'
        ];

        require_once __DIR__ . '/../Views/agendamentos/index.php';
    }

    public function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'nome'             => trim($_POST['nome'] ?? ''),
                'cpf'              => trim($_POST['cpf'] ?? ''),
                'telefone'         => trim($_POST['telefone'] ?? ''),
                'email'            => filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL),
                'especialidade'    => trim($_POST['especialidade'] ?? ''),
                'data_agendamento' => trim($_POST['data_agendamento'] ?? ($_POST['data'] ?? '')),
                'hora_agendamento' => trim($_POST['hora_agendamento'] ?? ($_POST['hora'] ?? ''))
            ];

            if ($this->model->salvarComPaciente($dados)) {
                $_SESSION['mensagem_sucesso'] = 'Agendamento realizado com sucesso!';
            } else {
                $_SESSION['mensagem_erro'] = 'Erro ao realizar o agendamento.';
            }

            header('Location: ' . $this->basePath . '/agendamentos');
            exit();
        }
    }

    public function atualizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'id'               => filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT),
                'paciente_id'      => filter_var($_POST['paciente_id'] ?? null, FILTER_VALIDATE_INT),
                'nome'             => trim($_POST['nome'] ?? ''),
                'cpf'              => trim($_POST['cpf'] ?? ''),
                'telefone'         => trim($_POST['telefone'] ?? ''),
                'email'            => filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL),
                'especialidade'    => trim($_POST['especialidade'] ?? ''),
                'data_agendamento' => trim($_POST['data_agendamento'] ?? ($_POST['data'] ?? '')),
                'hora_agendamento' => trim($_POST['hora_agendamento'] ?? ($_POST['hora'] ?? ''))
            ];

            if ($dados['id'] && $this->model->atualizar($dados)) {
                $_SESSION['mensagem_sucesso'] = 'Agendamento atualizado com sucesso!';
            } else {
                $_SESSION['mensagem_erro'] = 'Erro ao atualizar o agendamento.';
            }

            header('Location: ' . $this->basePath . '/agendamentos');
            exit();
        }
    }

    public function excluir(): void
    {

        $rawId = $_POST['id'] ?? $_GET['id'] ?? null;
        $id = filter_var($rawId, FILTER_VALIDATE_INT);

        if ($id && $this->model->excluir($id)) {
            $_SESSION['mensagem_sucesso'] = 'Agendamento excluído com sucesso!';
        } else {
            $_SESSION['mensagem_erro'] = 'Erro ou ID inválido para excluir agendamento.';
        }

        header('Location: ' . $this->basePath . '/agendamentos');
        exit();
    }
}