<?php

namespace App\Controllers;

use App\Models\Prontuario;

class ProntuarioController
{
    private Prontuario $prontuarioModel;

    public function __construct()
    {
        $this->prontuarioModel = new Prontuario();
    }

    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit();
        }

        $paciente   = null;
        $prontuario = null;
        $anexos     = [];

        $termo = !empty($_GET['busca']) ? $_GET['busca'] : (!empty($_GET['paciente_id']) ? $_GET['paciente_id'] : null);

        if ($termo) {
            $paciente = $this->prontuarioModel->buscarPacientePorIdOuCpf((string)$termo);
            if ($paciente) {
                $prontuario = $this->prontuarioModel->buscarUltimoProntuarioPorPacienteId((int)$paciente['id']);
                if ($prontuario) {
                    $anexos = $this->prontuarioModel->buscarAnexosPorProntuarioId((int)$prontuario['id']);
                }
            }
        }

        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        if ($basePath === '/' || $basePath === '\\') {
            $basePath = '';
        }

        $viewPath = __DIR__ . '/../Views/prontuarios/prontuarios.php';
        if (!file_exists($viewPath)) {
            $viewPath = __DIR__ . '/../Views/prontuarios/index.php';
        }

        require $viewPath;
    }

    public function salvar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pacienteId   = $_POST['paciente_id'] ?? null;
            $prontuarioId = !empty($_POST['prontuario_id']) ? (int)$_POST['prontuario_id'] : null;
            $consultaId   = !empty($_POST['consulta_id']) ? (int)$_POST['consulta_id'] : null;

            if (empty($pacienteId)) {
                header('Location: /prontuarios?erro=paciente_invalido');
                exit();
            }

            $dados = [
                'paciente_id'    => (int)$pacienteId,
                'funcionario_id' => $_SESSION['usuario_id'] ?? 1,
                'tipo_sanguineo' => $_POST['tipo_sanguineo'] ?? null,
                'diagnostico'    => $_POST['diagnostico'] ?? '',
                'prescricao'     => $_POST['prescricao'] ?? '',
                'status'         => 'ativo'
            ];

            $idFinalProntuario = $this->prontuarioModel->salvarProntuarioCompleto($prontuarioId, $dados, $_FILES);

            if ($idFinalProntuario) {
                if ($consultaId) {
                    $this->prontuarioModel->atualizarStatusConsulta($consultaId, 'Finalizado');
                }

                $pacienteAtual = $this->prontuarioModel->buscarPacientePorIdOuCpf((string)$pacienteId);
                $termoBusca    = $pacienteAtual['cpf'] ?? $pacienteId;
                header('Location: /prontuarios?busca=' . urlencode($termoBusca) . '&sucesso=1');
            } else {
                header('Location: /prontuarios?busca=' . urlencode((string)$pacienteId) . '&erro=salvar');
            }
            exit();
        }
    }
}