<?php

namespace app\Core;
use app\Core\Database;
use app\Helpers\Helper;
use Exception;

class Repository
{
    protected Database $db;
    protected string $table;
    protected object $model;

    public function __construct(string $table){
        $this->db = Database::getInstance();
        $this->table = $table;
    }

    public function setModel(object $model): void
    {
        $this->model = $model;
    }

    /**
     * @throws Exception
     */
    public function find(): array{
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";

        return $this->db->fetch($sql, ['id' => $this->model->getId()]) ?? [];
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
    public function create(): bool
    {
        $data = $this->extractData($this->model);
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
        $data = $this->extractData($this->model);
        $set = implode(', ', array_map(static fn($key) => "{$key} = :{$key}", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$set} WHERE id = :id";
        return (bool) $this->db->execute($sql, $data);
    }

    /**
     * @throws Exception
     */
    public function delete(): bool
    {
        $data = $this->extractData($this->model);

        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        return (bool) $this->db->execute($sql,$data);
    }

    public function extractData(object $model): array
    {
        $data = [];
        $reflection = new \ReflectionClass($model);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $getter = 'get' . $property->getName();
            if (method_exists($model, $getter)) {
                $data[$property->getName()] = $model->$getter();
            }
        }

        return $data;
    }

}