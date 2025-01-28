<?php

namespace app\Core;
use app\Core\Database;
use Exception;

class Repository
{
    protected Database $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * @throws Exception
     */
    public function find(int $Id): array{
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";

        return $this->db->fetch($sql, ['id' => $Id]) ?? [];
    }

    /**
     * @throws Exception
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";

        return $this->db->fetchAll($sql) ?? [];
    }

    /**
     * @throws Exception
     */
    public function create(array $data): bool
    {
        $cols = implode(', ', array_keys($data));
        $values = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$cols}) VALUES ({$values})";
        return (bool) $this->db->execute($sql,$data);
    }

    /**
     * @throws Exception
     */
    public function update(array $data, int $id): bool
    {
        $set = implode(', ', array_map(fn($key) => "{$key} = :{$key}", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$set} WHERE id = :id";
        $data['id'] = $id;
        return (bool) $this->db->execute($sql, $data);
    }

    /**
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        return (bool) $this->db->execute($sql,['id' => $id]);
    }
}