<?php

namespace App\Models;

use Config\Database;
use PDO;
use Exception;
use finfo;

class Prontuario
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function buscarPacientePorIdOuCpf(string $termo): ?array
    {
        $cpfLimpo = preg_replace('/[^0-9]/', '', $termo);

        $sql = "
            SELECT p.*, TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE()) AS idade 
            FROM pacientes p 
            WHERE p.id = :termo_id 
               OR p.cpf = :termo_cpf 
               OR REPLACE(REPLACE(p.cpf, '.', ''), '-', '') = :cpf_limpo 
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':termo_id'  => is_numeric($termo) ? (int)$termo : 0,
            ':termo_cpf' => $termo,
            ':cpf_limpo' => $cpfLimpo
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function buscarUltimoProntuarioPorPacienteId(int $pacienteId): ?array
    {
        $sql = "SELECT * FROM prontuarios WHERE paciente_id = :paciente_id ORDER BY id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':paciente_id' => $pacienteId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function buscarAnexosPorProntuarioId(int $prontuarioId): array
    {
        $sql = "SELECT * FROM prontuario_anexos WHERE prontuario_id = :prontuario_id ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':prontuario_id' => $prontuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function atualizarStatusConsulta(int $consultaId, string $status = 'Finalizado'): bool
    {
        $sql = "UPDATE agendamentos SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id'     => $consultaId
        ]);
    }

    public function salvarProntuarioCompleto(?int $id, array $dados, array $arquivos): ?int
    {
        $arquivosCriados = [];

        try {
            $this->pdo->beginTransaction();

            $pacienteId = $dados['paciente_id'] ?? null;

            if ($id && $id > 0) {
                $sql = "
                    UPDATE prontuarios 
                    SET tipo_sanguineo = :tipo_sanguineo, 
                        diagnostico = :diagnostico, 
                        prescricao = :prescricao 
                    WHERE id = :id
                ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':tipo_sanguineo' => $dados['tipo_sanguineo'] ?? null,
                    ':diagnostico'    => $dados['diagnostico'] ?? null,
                    ':prescricao'     => $dados['prescricao'] ?? null,
                    ':id'             => $id
                ]);
                $prontuarioIdFinal = $id;

                // Caso o paciente_id não venha no $dados, busca do banco para atualizar o tipo sanguíneo
                if (!$pacienteId) {
                    $stmtSearch = $this->pdo->prepare("SELECT paciente_id FROM prontuarios WHERE id = :id");
                    $stmtSearch->execute([':id' => $id]);
                    $pacienteId = $stmtSearch->fetchColumn();
                }
            } else {
                $sql = "
                    INSERT INTO prontuarios (paciente_id, funcionario_id, tipo_sanguineo, diagnostico, prescricao, status) 
                    VALUES (:paciente_id, :funcionario_id, :tipo_sanguineo, :diagnostico, :prescricao, :status)
                ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':paciente_id'    => $pacienteId,
                    ':funcionario_id' => $dados['funcionario_id'],
                    ':tipo_sanguineo' => $dados['tipo_sanguineo'] ?? null,
                    ':diagnostico'    => $dados['diagnostico'] ?? null,
                    ':prescricao'     => $dados['prescricao'] ?? null,
                    ':status'         => $dados['status'] ?? 'Ativo'
                ]);
                $prontuarioIdFinal = (int)$this->pdo->lastInsertId();
            }

            if (!empty($dados['tipo_sanguineo']) && !empty($pacienteId)) {
                $sqlPaciente = "UPDATE pacientes SET tipo_sanguineo = :ts WHERE id = :paciente_id";
                $stmtPaciente = $this->pdo->prepare($sqlPaciente);
                $stmtPaciente->execute([
                    ':ts'          => $dados['tipo_sanguineo'],
                    ':paciente_id' => $pacienteId
                ]);
            }

            if (!empty($arquivos['anexos']['name'][0])) {
                $arquivosCriados = $this->processarUploadAnexos($prontuarioIdFinal, $arquivos['anexos']);
            }

            $this->pdo->commit();
            return $prontuarioIdFinal;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            // Remove arquivos salvos fisicamente caso a transação falhe
            foreach ($arquivosCriados as $caminhoAbsoluto) {
                if (file_exists($caminhoAbsoluto)) {
                    @unlink($caminhoAbsoluto);
                }
            }

            error_log("Erro ao salvar prontuário: " . $e->getMessage());
            return null;
        }
    }

    private function processarUploadAnexos(int $prontuarioId, array $files): array
    {
        $diretorioUpload = __DIR__ . '/../../public/uploads/exames/';
        $mimesPermitidos = [
            'pdf'  => 'application/pdf',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
        ];

        if (!is_dir($diretorioUpload)) {
            mkdir($diretorioUpload, 0755, true);
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $arquivosSalvos = [];

        foreach ($files['tmp_name'] as $index => $tmpName) {
            if ($files['error'][$index] === UPLOAD_ERR_OK) {
                $nomeOriginal = basename($files['name'][$index]);
                $extensao     = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

                if (!array_key_exists($extensao, $mimesPermitidos)) {
                    continue;
                }

                $mimeTypeReal = $finfo->file($tmpName);
                if ($mimeTypeReal !== $mimesPermitidos[$extensao]) {
                    continue;
                }

                $novoNome   = uniqid('exame_', true) . '.' . $extensao;
                $caminhoAbs = $diretorioUpload . $novoNome;
                $caminhoRel = 'uploads/exames/' . $novoNome;

                if (move_uploaded_file($tmpName, $caminhoAbs)) {
                    $arquivosSalvos[] = $caminhoAbs;

                    $sql = "
                        INSERT INTO prontuario_anexos (prontuario_id, nome_original, caminho) 
                        VALUES (:prontuario_id, :nome_original, :caminho)
                    ";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([
                        ':prontuario_id' => $prontuarioId,
                        ':nome_original' => $nomeOriginal,
                        ':caminho'       => $caminhoRel
                    ]);
                }
            }
        }

        return $arquivosSalvos;
    }
}