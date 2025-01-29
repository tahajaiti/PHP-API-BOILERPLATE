<?php

namespace app\Core;

use Exception;

abstract class Model
{

    protected int $id;

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
        return get_object_vars($this);
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
        throw new \RuntimeException("Method {name} not found in " . static::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

}