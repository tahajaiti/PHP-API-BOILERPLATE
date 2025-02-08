<?php

namespace app\Interfaces;

use app\Model\Model;

/**
 * Interface RepositoryInt
 *
 * Defines the contract for basic CRUD operations.
 */
interface RepositoryInt
{
    /**
     * Searches for and retrieves a specific instance of ModelInt.
     *
     * @return Model|null Returns the found ModelInt instance or null if not found.
     */
    public function find(): ?Model;

    /**
     * Retrieves all instances of the associated model.
     *
     * @return array Returns an array containing all instances of the model.
     */
    public function findAll(): array;

    /**
     * Creates a new entity or resource in the system.
     *
     * @return bool Returns true if the creation was successful, false otherwise.
     */
    public function create(): bool;

    /**
     * Updates the current instance with new data.
     *
     * @return bool Returns true if the update was successful, false otherwise.
     */
    public function update(): bool;

    /**
     * Deletes the specified resource or entity.
     *
     * @return bool Returns true if the deletion was successful, false otherwise.
     */
    public function delete(): bool;
}