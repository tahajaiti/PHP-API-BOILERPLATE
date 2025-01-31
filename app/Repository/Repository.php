<?php

namespace app\Repository;
use app\Core\Database;
use app\Core\QueryBuilder;
use app\Helpers\Helper;
use app\Model\Model;
use Exception;

class Repository
{
    protected Database $db;
    protected Model $model;
    protected string $table;
    protected QueryBuilder $query;

    public function __construct(string $table){
        $this->db = Database::getInstance();
        $this->table = $table;
        $this->query = new QueryBuilder($table);
    }

    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    /**
     * @throws Exception
     */
    public function find(): ?Model{
        $this->query->select()->where('id', '=', $this->model->getId());

        $sql = $this->query->getQuery();
        $params = $this->query->getParams();

        $data = $this->db->fetch($sql, $params);

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
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return (bool) $this->db->execute($sql,['id' => $this->model->getId()]);
    }

    protected function getModelClass(): string
    {
        return get_class($this->model);
    }

}