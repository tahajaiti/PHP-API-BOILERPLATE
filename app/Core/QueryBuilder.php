<?php

namespace app\Core;

class QueryBuilder
{
    private string $table;
    private array $select = [];
    private array $joins = [];
    private array $wheres = [];
    private array $groupBy = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $params = [];
    private string $type = 'select'; // either select, insert, update, delete

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function select(array $columns = ['*']): self
    {
        $this->type = 'select';
        $this->select = $columns;
        return $this;
    }

    public function insert(array $data): self
    {
        $this->type = 'insert';
        $this->params = $data;
        return $this;
    }

    public function update(array $data): self
    {
        $this->type = 'update';
        $this->params = $data;
        return $this;
    }

    public function delete(): self
    {
        $this->type = 'delete';
        return $this;
    }

    public function join(string $table, string $condition, string $type = 'INNER'): self
    {
        $this->joins[] = "$type JOIN $table ON $condition";
        return $this;
    }

    public function where(string $column, string $operator, $value): self
    {
        $this->wheres[] = "$column $operator :$column";
        $this->params[":$column"] = $value;
        return $this;
    }

    public function groupBy(string $column): self
    {
        $this->groupBy[] = $column;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = "$column $direction";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function getQuery(): string
    {
        return match ($this->type) {
            'select' => $this->buildSelectQuery(),
            'insert' => $this->buildInsertQuery(),
            'update' => $this->buildUpdateQuery(),
            'delete' => $this->buildDeleteQuery(),
            default => throw new \RuntimeException("Invalid query type: {$this->type}"),
        };
    }

    public function getParams(): array
    {
        return $this->params;
    }

    private function buildSelectQuery(): string
    {
        $columns = implode(', ', $this->select);
        $query = "SELECT $columns FROM {$this->table}";

        if (!empty($this->joins)) {
            $query .= ' ' . implode(' ', $this->joins);
        }

        if (!empty($this->wheres)) {
            $query .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        if (!empty($this->groupBy)) {
            $query .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }

        if (!empty($this->orderBy)) {
            $query .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $query .= " LIMIT $this->limit";
        }

        if ($this->offset !== null) {
            $query .= " OFFSET $this->offset";
        }

        return $query;
    }

    private function buildInsertQuery(): string
    {
        $columns = implode(', ', array_keys($this->params));
        $values = ':' . implode(', :', array_keys($this->params));
        return "INSERT INTO {$this->table} ($columns) VALUES ($values)";
    }

    private function buildUpdateQuery(): string
    {
        $set = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($this->params)));
        $query = "UPDATE {$this->table} SET $set";

        if (!empty($this->wheres)) {
            $query .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        return $query;
    }

    private function buildDeleteQuery(): string
    {
        $query = "DELETE FROM {$this->table}";

        if (!empty($this->wheres)) {
            $query .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        return $query;
    }
}