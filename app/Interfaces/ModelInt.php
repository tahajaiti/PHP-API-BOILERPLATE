<?php

namespace app\Model;

/**
 * Interface representing a general model with basic functionality for data handling and serialization.
 */
interface ModelInt
{
    /**
     * Populates the object's properties with the provided data array.
     *
     * @param array $data An associative array where keys match the property names and values are the property values to set.
     * @return void
     */
    public function fill(array $data): void;

    /**
     * Converts the object's properties into an associative array.
     *
     * @return array An associative array representing the object's properties and their values.
     */
    public function toArray(): array;

    /**
     * Retrieves the unique identifier of the object.
     *
     * @return int The unique identifier of the object.
     */
    public function getId(): int;

    /**
     * Prepares the data for JSON serialization.
     *
     * @return mixed The data that can be serialized by json_encode, which can be any type except a resource.
     */
    public function jsonSerialize(): mixed;
}