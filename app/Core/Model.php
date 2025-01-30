<?php

namespace app\Core;

use Exception;
use JsonSerializable;
use ReflectionClass;
use RuntimeException;

abstract class Model implements JsonSerializable
{

    protected int $id = 0;

    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    public function fill(array $data): void
    {
        foreach ($data as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            } else {
                $this->$key = $value;
            }
        }
    }

    public function toArray(): array
    {
        $data = [];
        $reflection = new ReflectionClass($this);
        foreach ($reflection->getProperties() as $property) {
            $data[$property->getName()] = $property->getValue($this);
        }
        return $data;
    }

    /**
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (preg_match('/^(get|set)([A-Z][a-zA-Z0-9]*)$/', $name, $matches)) {
            $property = lcfirst($matches[2]);
            $reflection = new ReflectionClass($this);
            if ($reflection->hasProperty($property)) {
                $propertyReflection = $reflection->getProperty($property);
                if ($matches[1] === 'get') {
                    return $propertyReflection->getValue($this);
                }
                if ($matches[1] === 'set') {
                    $propertyReflection->setValue($this, $arguments[0]);
                    return $this;
                }
            }
        }
        throw new RuntimeException("Method $name not found in " . static::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    abstract public function jsonSerialize(): mixed;

}