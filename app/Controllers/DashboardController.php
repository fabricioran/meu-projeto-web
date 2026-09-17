<?php

namespace App\Controllers;

use App\Models\Dashboard;

class DashboardController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['erro_login'] = 'Efetue o login para acessar o painel.';
            header('Location: /login');
            exit;
        }
    }

    public function index()
    {
        $model = new Dashboard();

        $stats = [
            'consultas_hoje'    => $model->consultasHoje(),
            'total_pacientes'   => $model->totalPacientes(),
            'total_prontuarios' => $model->laudosPendentes(),
            'aguardando'        => $model->taxaOcupacao()
        ];

        $consultasHoje = $model->listarAgendamentosHoje();

        // Define a variável $basePath para uso na View
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

        require_once __DIR__ . '/../Views/dashboard/index.php';
    }
}
