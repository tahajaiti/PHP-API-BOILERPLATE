<?php

namespace app\Interfaces;

use app\Core\Request;
use app\Model\ModelInt;

interface ServiceInt
{
    /**
     * Fetches all records from the repository or Redis cache.
     *
     * @return array
     */
    public function getAll(): array;

    /**
     * Fetches a record by ID from the repository or Redis cache.
     *
     * @param int $id
     * @return ModelInt|null
     */
    public function getById(int $id): ?ModelInt;

    /**
     * Creates a new record in the repository.
     *
     * @param Request $data
     * @return bool
     */
    public function create(Request $data): bool;

    /**
     * Updates an existing record in the repository.
     *
     * @param Request $data
     * @return bool
     */
    public function update(Request $data): bool;

    /**
     * Deletes a record by ID from the repository.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}