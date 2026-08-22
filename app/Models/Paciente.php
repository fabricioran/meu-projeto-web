<?php

namespace App\Models;

use Config\Database; 
use PDO;

class Paciente
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function listar()
    {
        // Pega a data e a hora salvas na coluna 'created_at' do banco
        $sql = "SELECT id, nome, cpf, telefone, email, 
                DATE_FORMAT(created_at, '%d/%m/%Y') as data_cadastro, 
                DATE_FORMAT(created_at, '%H:%i') as hora_cadastro 
                FROM pacientes 
                ORDER BY id ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM pacientes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function inserir($nome, $cpf, $telefone, $email)
    {
        $sql = "INSERT INTO pacientes (nome, cpf, telefone, email) VALUES (:nome, :cpf, :telefone, :email)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome'     => $nome,
            ':cpf'      => $cpf,
            ':telefone' => $telefone,
            ':email'    => $email
        ]);
    }

    public function atualizar($id, $nome, $cpf, $telefone, $email)
    {
        $sql = "UPDATE pacientes SET nome = :nome, cpf = :cpf, telefone = :telefone, email = :email WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'       => $id,
            ':nome'     => $nome,
            ':cpf'      => $cpf,
            ':telefone' => $telefone,
            ':email'    => $email
        ]);
    }

    public function excluir($id)
    {
        $sql = "DELETE FROM pacientes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $resultado = $stmt->execute();

        // Se excluiu com sucesso, reordena a sequência do ID (1, 2, 3...)
        if ($resultado) {
            $this->db->exec("SET @count = 0;");
            $this->db->exec("UPDATE pacientes SET id = (@count := @count + 1);");
            $this->db->exec("ALTER TABLE pacientes AUTO_INCREMENT = 1;");
        }

        return $resultado;
    }
}