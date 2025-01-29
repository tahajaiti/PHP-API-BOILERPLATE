<?php

namespace app\Core;

use Exception;

abstract  class Service{

    protected Repository $repository;

    public function setRepository(Repository $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @throws Exception
     */
    public function create(Request $request): object
    {
        $this->validate($request);
        $model = $this->mapToModel($request);
        $this->repository->setModel($model);
        $result = $this->repository->create();

        if (!$result) {
            throw new \RuntimeException('Failed to create model');
        }

        return $model;
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object
    {
        $this->validate($request);
        $model = $this->findModelById($request->get('id'));
        $model->fill($data);
        $this->repository->setModel($model);
        $result = $this->repository->update();

        if (!$result) {
            throw new \RuntimeException("Failed to update entity.");
        }

        return $model;
    }

    /**
     * @throws Exception
     */
    protected function findModelById(int $id): object{
        $model = $this->getModel();
        $model->setId($id);
        $this->repository->setModel($model);
        $result = $this->repository->find();

        if (empty($result)) {
            throw new \RuntimeException("Entity not found.");
        }

        return $model;
    }

    abstract protected function validate(Request $request);
    abstract protected function mapToModel(Request $request): object;
    abstract protected function getModel();
}