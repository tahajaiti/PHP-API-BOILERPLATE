<?php

namespace app\Core;

class QueryBuilder
{
    private string $table;
    private string $query = '';
    private array $params = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function select(string $cols = '*'): QueryBuilder
    {
        $this->query .= "SELECT $cols FROM $this->table";
        return $this;
    }

    public function from(string $table): QueryBuilder
    {
        $this->query .= " FROM $table";
        return $this;
    }

    public function where(string $col, string $operator, string $value): QueryBuilder
    {
        $this->query .= " WHERE $col $operator :$col";
        $this->params[$col] = $value;
        return $this;
    }

    public function andWhere(string $col, string $operator, string $value): QueryBuilder
    {
        $this->query .= " AND $col $operator ?";
        $this->params[] = $value;
        return $this;
    }

    public function orWhere(string $col, string $operator, string $value): QueryBuilder
    {
        $this->query .= " OR $col $operator ?";
        $this->params[] = $value;
        return $this;
    }

    public function insert(array $data): QueryBuilder
    {
        unset($data["id"]);
        $cols = implode(', ', array_keys($data));
        $values = ":" . implode(", :", array_keys($data));

        $this->query = "INSERT INTO {$this->table} ({$cols}) VALUES ({$values})";
        $this->params = array_values($data);
        return $this;
    }

    public function update(array $data): QueryBuilder
    {
        unset($data["id"]);
        $set = implode(', ', array_map(static fn($key) => "{$key} = :{$key}", array_keys($data)));
        $this->query = "UPDATE {$this->table} SET {$set}";
        $this->params = $data;
        return $this;
    }

    public function delete(): QueryBuilder
    {
        $this->query = "DELETE FROM {$this->table}";
        return $this;
    }

    public function getQuery(): string
    {
        $query = $this->query;
        $this->query = '';
        return $query;
    }

    public function getParams(): array
    {
        $params = $this->params;
        $this->params = [];
        return $params;
    }
}