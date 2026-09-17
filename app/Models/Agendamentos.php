<?php

namespace App\Models;

use PDO;
use Exception;

class Agendamentos
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarTodos(): array
    {
        $sql = "
            SELECT 
                a.id AS agendamento_id,
                a.id,
                a.paciente_id,
                p.nome,
                p.cpf,
                p.telefone,
                p.email,
                a.especialidade,
                a.data,
                a.hora,
                a.data AS data_agendamento,
                a.hora AS hora_agendamento,
                a.status
            FROM agendamentos a
            INNER JOIN pacientes p ON a.paciente_id = p.id
            ORDER BY a.id ASC
        ";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salvarComPaciente(array $dados): bool
    {
        try {
            $this->pdo->beginTransaction();

            $sqlPac = "SELECT id FROM pacientes WHERE cpf = :cpf LIMIT 1";
            $stmtPac = $this->pdo->prepare($sqlPac);
            $stmtPac->execute([':cpf' => $dados['cpf']]);
            $paciente = $stmtPac->fetch(PDO::FETCH_ASSOC);

            if ($paciente) {
                $pacienteId = $paciente['id'];
                $sqlUpdatePac = "
                    UPDATE pacientes 
                    SET nome = :nome, telefone = :telefone, email = :email 
                    WHERE id = :id
                ";
                $stmtUpdate = $this->pdo->prepare($sqlUpdatePac);
                $stmtUpdate->execute([
                    ':nome'     => $dados['nome'],
                    ':telefone' => $dados['telefone'] ?? '',
                    ':email'    => $dados['email'],
                    ':id'       => $pacienteId
                ]);
            } else {
                $sqlInsPac = "
                    INSERT INTO pacientes (nome, cpf, telefone, email, data_cadastro, hora_cadastro) 
                    VALUES (:nome, :cpf, :telefone, :email, CURDATE(), CURTIME())
                ";
                $stmtIns = $this->pdo->prepare($sqlInsPac);
                $stmtIns->execute([
                    ':nome'     => $dados['nome'],
                    ':cpf'      => $dados['cpf'],
                    ':telefone' => $dados['telefone'] ?? '',
                    ':email'    => $dados['email']
                ]);
                $pacienteId = (int) $this->pdo->lastInsertId();
            }

            $sqlAgend = "
                INSERT INTO agendamentos (paciente_id, especialidade, data, hora, status) 
                VALUES (:paciente_id, :especialidade, :data, :hora, 'Aguardando')
            ";
            $stmtAgend = $this->pdo->prepare($sqlAgend);
            $stmtAgend->execute([
                ':paciente_id'   => $pacienteId,
                ':especialidade' => $dados['especialidade'] ?? '',
                ':data'          => $dados['data_agendamento'],
                ':hora'          => $dados['hora_agendamento']
            ]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Erro em Agendamentos::salvarComPaciente: " . $e->getMessage());
            return false;
        }
    }

    public function atualizar(array $dados): bool
    {
        try {
            $this->pdo->beginTransaction();

            $pacienteId = $dados['paciente_id'] ?? null;

            if (empty($pacienteId) && !empty($dados['id'])) {
                $sqlFindPac = "SELECT paciente_id FROM agendamentos WHERE id = :id LIMIT 1";
                $stmtFindPac = $this->pdo->prepare($sqlFindPac);
                $stmtFindPac->execute([':id' => $dados['id']]);
                $agendamento = $stmtFindPac->fetch(PDO::FETCH_ASSOC);

                if ($agendamento) {
                    $pacienteId = $agendamento['paciente_id'];
                }
            }

            if (!empty($pacienteId)) {
                // Impede que a alteração de CPF duplique o cadastro de outro paciente
                $sqlCheckCpf = "SELECT id FROM pacientes WHERE cpf = :cpf AND id != :paciente_id LIMIT 1";
                $stmtCheckCpf = $this->pdo->prepare($sqlCheckCpf);
                $stmtCheckCpf->execute([
                    ':cpf'         => $dados['cpf'],
                    ':paciente_id' => $pacienteId
                ]);

                if ($stmtCheckCpf->fetch()) {
                    throw new Exception("O CPF informado já pertence a outro paciente.");
                }

                $sqlPac = "
                    UPDATE pacientes 
                    SET nome = :nome, cpf = :cpf, telefone = :telefone, email = :email 
                    WHERE id = :paciente_id
                ";
                $stmtPac = $this->pdo->prepare($sqlPac);
                $stmtPac->execute([
                    ':nome'        => $dados['nome'],
                    ':cpf'         => $dados['cpf'],
                    ':telefone'    => $dados['telefone'] ?? '',
                    ':email'       => $dados['email'],
                    ':paciente_id' => $pacienteId
                ]);
            }

            $sqlAgend = "
                UPDATE agendamentos 
                SET especialidade = :especialidade, 
                    data = :data, 
                    hora = :hora 
                WHERE id = :id
            ";
            $stmtAgend = $this->pdo->prepare($sqlAgend);
            $stmtAgend->execute([
                ':especialidade' => $dados['especialidade'] ?? '',
                ':data'          => $dados['data_agendamento'],
                ':hora'          => $dados['hora_agendamento'],
                ':id'            => $dados['id']
            ]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Erro em Agendamentos::atualizar: " . $e->getMessage());
            return false;
        }
    }

    public function excluir(int $id): bool
    {
        try {
            $sql = "DELETE FROM agendamentos WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute() && $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Erro em Agendamentos::excluir: " . $e->getMessage());
            return false;
        }
    }

    // Alias para manter compatibilidade com Controllers chamando deletarAgendamento
    public function deletarAgendamento(int $id): bool
    {
        return $this->excluir($id);
    }
}