<?php

namespace App\Models;

use Config\Database;
use PDO;

class Funcionario
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM funcionarios WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function atualizarSenha(int $id, string $senhaHash): bool
    {
        $stmt = $this->pdo->prepare("UPDATE funcionarios SET senha = :senha WHERE id = :id");
        $stmt->bindValue(':senha', $senhaHash);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function listarTodos(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM funcionarios ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function inserir(string $nome, string $email, string $senha, string $perfil): bool
    {
        $sql = "
            INSERT INTO funcionarios (nome, email, senha, perfil, ativo) 
            VALUES (:nome, :email, :senha, :perfil, 1)
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':senha', $senha);
        $stmt->bindValue(':perfil', $perfil);
        return $stmt->execute();
    }

    public function alterarStatus(int $id, string $novoStatus): bool
    {
        $ativo = ($novoStatus === 'ativo') ? 1 : 0;
        $stmt = $this->pdo->prepare("UPDATE funcionarios SET ativo = :ativo WHERE id = :id");
        $stmt->bindValue(':ativo', $ativo, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function excluir(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM funcionarios WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}