<?php

namespace App\Models;

use Config\Database;
use PDO;

class Dashboard
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function totalPacientes(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM pacientes");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    public function consultasHoje(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM agendamentos");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    public function laudosPendentes(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM prontuarios WHERE status = 'ativo'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    public function taxaOcupacao(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM agendamentos WHERE (status = 'Aguardando' OR status IS NULL OR status = '')");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    public function listarAgendamentosHoje(): array
    {
        // Busca os agendamentos cadastrados trazendo Paciente, Médico e Especialidade
        $sql = "SELECT 
                    a.id, 
                    a.hora, 
                    COALESCE(a.status, 'Aguardando') AS status, 
                    a.especialidade, 
                    a.paciente_id, 
                    p.nome AS paciente_nome, 
                    f.nome AS medico_nome
                FROM agendamentos a
                LEFT JOIN pacientes p ON a.paciente_id = p.id
                LEFT JOIN funcionarios f ON a.funcionario_id = f.id
                ORDER BY a.id DESC
                LIMIT 10";
                
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}