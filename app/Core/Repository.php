<?php

namespace app\Core;
use app\Core\Database;
use app\Helpers\Helper;
use Exception;

class Repository
{
    protected Database $db;
    protected string $table;
    protected Model $model;

    public function __construct(string $table){
        $this->db = Database::getInstance();
        $this->table = $table;
    }

    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    /**
     * @throws Exception
     */
    public function find(): ?Model{
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $data = $this->db->fetch($sql, ["id" => $this->model->getId()]);

        return $data ? new ($this->getModelClass())($data) : null;
    }

    /**
     * @throws Exception
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }

    /**
     * @throws Exception
     */
    public function create(): bool
    {
        $data = $this->model->toArray();
        unset($data["id"]);
        $cols = implode(', ', array_keys($data));
        $values = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$cols}) VALUES ({$values})";
        return (bool) $this->db->execute($sql,$data);
    }

    /**
     * @throws Exception
     */
    public function update(): bool
    {
        $data = $this->model->toArray();
        $set = implode(', ', array_map(static fn($key) => "{$key} = :{$key}", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$set} WHERE id = :id";
        return (bool) $this->db->execute($sql, $data);
    }

    /**
     * @throws Exception
     */
    public function delete(): bool
    {
        $data = $this->model->toArray();

        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        return (bool) $this->db->execute($sql,$data);
    }

    protected function getModelClass(): string
    {
        return get_class($this->model);
    }

}