<?php

namespace App\Models;

use Config\Database;
use PDO;

class Paciente
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function listar(): array
    {
        $sql = "
            SELECT 
                id, 
                nome, 
                cpf, 
                telefone, 
                email, 
                DATE_FORMAT(created_at, '%d/%m/%Y') AS data_cadastro, 
                DATE_FORMAT(created_at, '%H:%i') AS hora_cadastro 
            FROM pacientes 
            ORDER BY id ASC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT * FROM pacientes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function inserir(string $nome, string $cpf, string $telefone, string $email): bool
    {
        $sql = "
            INSERT INTO pacientes (nome, cpf, telefone, email) 
            VALUES (:nome, :cpf, :telefone, :email)
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome'     => $nome,
            ':cpf'      => $cpf,
            ':telefone' => $telefone,
            ':email'    => $email
        ]);
    }

    public function atualizar(int $id, string $nome, string $cpf, string $telefone, string $email): bool
    {
        $sql = "
            UPDATE pacientes 
            SET nome = :nome, cpf = :cpf, telefone = :telefone, email = :email 
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'       => $id,
            ':nome'     => $nome,
            ':cpf'      => $cpf,
            ':telefone' => $telefone,
            ':email'    => $email
        ]);
    }

    public function excluir(int $id): bool
    {
        $sql = "DELETE FROM pacientes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $resultado = $stmt->execute();

        if ($resultado) {
            $this->db->exec("SET @count = 0;");
            $this->db->exec("UPDATE pacientes SET id = (@count := @count + 1);");
            $this->db->exec("ALTER TABLE pacientes AUTO_INCREMENT = 1;");
        }

        return $resultado;
    }
}
