<?php

namespace App\Models;

use Core\Model;
use PDO;

class Funcionario extends Model
{
    
     //Insere um novo funcionário aplicando hash seguro na senha.
     
    public function inserir($nome, $email, $senha, $perfil = 'funcionario')
    {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO funcionarios (nome, email, senha, perfil) 
                VALUES (:nome, :email, :senha, :perfil)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':perfil', $perfil);

        return $stmt->execute();
    }

    
     //Lista todos os funcionários cadastrados.
     
    public function listar()
    {
        $sql = "SELECT id, nome, email, perfil, criado_em FROM funcionarios";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // Busca um funcionário pelo ID.
     
    public function buscarPorId($id)
    {
        $sql = "SELECT id, nome, email, perfil FROM funcionarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    // Busca um funcionário pelo e-mail.
     
    public function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM funcionarios WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
} 
