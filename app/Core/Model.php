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

    public function fill(array $data): void{
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
            $property->setAccessible(true);
            $data[$property->getName()] = $property->getValue($this);
        }
        return $data;
    }

    /**
     * @throws Exception
     */
    public function __call($name, $arguments){
        if (preg_match('/^(get|set)([A-Z][a-zA-Z0-9]*)$/', $name, $matches)) {
            $property = lcfirst($matches[2]);
            if ($matches[1] === 'get' && property_exists($this, $property)) {
                return $this->$property;
            }
            if ($matches[1] === 'set' && property_exists($this, $property)) {
                $this->$property = $arguments[0];
                return $this;
            }
        }
        throw new RuntimeException("Method {name} not found in " . static::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    abstract public function jsonSerialize(): mixed;

}