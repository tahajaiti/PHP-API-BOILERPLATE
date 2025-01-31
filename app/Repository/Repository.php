<?php

namespace app\Repository;

use app\Core\Database;
use app\Core\QueryBuilder;
use app\Model\Model;

class Repository implements RepositoryInt
{
    protected Database $db;
    protected Model $model;
    protected string $table;
    protected QueryBuilder $query;

    public function __construct(string $table)
    {
        $this->db = Database::getInstance();
        $this->table = $table;
        $this->query = new QueryBuilder($table);
    }

    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    public function find(): ?Model
    {
        $this->query->select()
            ->where('id', '=', $this->model->getId())
            ->orderBy('id', 'ASC');

        $sql = $this->query->getQuery();
        $params = $this->query->getParams();

        $data = $this->db->fetch($sql, $params);

        return $data ? new ($this->getModelClass())($data) : null;
    }


    public function findAll(): array
    {
        $this->query->select();
        $sql = $this->query->getQuery();
        return $this->db->fetchAll($sql);
    }


    public function create(): bool
    {
        $data = $this->model->toArray();
        $this->query->insert($data);
        $sql = $this->query->getQuery();

        return (bool)$this->db->execute($sql, $data);
    }


    public function update(): bool
    {
        $data = $this->model->toArray();
        unset($data['id']); //because we don't want to update the id
        $this->query->update($data)->where('id', '=', $this->model->getId());

        $sql = $this->query->getQuery();
        $params = $this->query->getParams();

        return (bool)$this->db->execute($sql, $params);
    }


    public function delete(): bool
    {
        $sql = $this->query->delete()->where('id', '=', $this->model->getId())->getQuery();
        $param = $this->query->getParams();
        return (bool)$this->db->execute($sql, $param);
    }

    protected function getModelClass(): string
    {
        return get_class($this->model);
    }

}