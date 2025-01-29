<?php

namespace app\Core;

use app\Helpers\Helper;
use Exception;
use RuntimeException;

abstract class Service
{
    protected Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws Exception
     */
    public function getAll(): array
    {
        $model = $this->getModelClass();
        $model = new $model();
        $this->repository->setModel($model);
       return $this->repository->findAll();
    }

    /**
     * @throws Exception
     */
    public function getById(int $id): ?Model{
        $model = $this->findModelById($id);
        $this->repository->setModel($model);

        return $this->repository->find();
    }

    /**
     * @throws Exception
     */
    public function create(Request $data): ?Model
    {
        if (!$this->validate($data, true)){
            return null;
        }
        $model = $this->mapToModel($data);
        $this->repository->setModel($model);

        if (!$this->repository->create()) {
            throw new RuntimeException('Failed to create model');
        }

        return $model;
    }

    /**
     * @throws Exception
     */
    public function update(Request $data): ?Model
    {
        if (!$this->validate($data, false)){
            return null;
        }
        $model = $this->findModelById($data->get('id'));
        $model->fill($data->all());
        $this->repository->setModel($model);

        if (!$this->repository->update()) {
            throw new RuntimeException("Failed to update entity.");
        }

        return $model;
    }

    /**
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $model = $this->findModelById($id);
        $this->repository->setModel($model);

        return $this->repository->delete();
    }

    /**
     * @throws Exception
     */
    protected function findModelById(int $id): Model
    {
        $modelClass = $this->getModelClass();
        $model = new $modelClass(['id' => $id]);
        $this->repository->setModel($model);
        $result = $this->repository->find();

        if (!$result) {
            throw new RuntimeException("Entity not found.");
        }

        return $result;
    }

    abstract protected function validate(Request $data, bool $isCreate): bool;
    abstract protected function mapToModel(Request $data): Model;
    abstract protected function getModelClass(): string;
}
