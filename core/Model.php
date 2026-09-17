<?php

namespace Core;

use Config\Database;
use PDO;

abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    protected function findById(string $table, int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    protected function findAll(string $table): array
    {
        $stmt = $this->db->query("SELECT * FROM {$table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function deleteById(string $table, int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}