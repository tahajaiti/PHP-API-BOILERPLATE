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
        $this->query->select();
        $sql = $this->query->getQuery();
        return $this->db->fetchAll($sql);
    }

    /**
     * @throws Exception
     */
    public function create(): bool
    {
        $data = $this->model->toArray();
        $this->query->insert($data);
        $sql = $this->query->getQuery();

        return (bool) $this->db->execute($sql,$data);
    }

    /**
     * @throws Exception
     */
    public function update(): bool
    {
        $data = $this->model->toArray();

        $this->query->update($data)->where('id', '=', $this->model->getId());

        $sql = $this->query->getQuery();
        $params = $this->query->getParams();

        return (bool) $this->db->execute($sql, $params);
    }

    /**
     * @throws Exception
     */
    public function delete(): bool
    {
        $sql = $this->query->delete()->where('id', '=', $this->model->getId())->getQuery();
        $param = $this->query->getParams();
        return (bool) $this->db->execute($sql,$param);
    }

    protected function getModelClass(): string
    {
        return get_class($this->model);
    }

}