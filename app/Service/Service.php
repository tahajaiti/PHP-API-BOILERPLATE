<?php

namespace app\Service;

use app\Core\RedisClient;
use app\Core\Request;
use app\Model\Model;
use app\Repository\Repository;
use Exception;
use JsonException;
use RuntimeException;

abstract class Service
{
    protected Repository $repository;
    protected \Redis $redis;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->redis = RedisClient::getRedis();
    }

    /**
     * Fetches all records from the repository or Redis cache.
     *
     * @return array
     * @throws JsonException
     * @throws Exception
     */
    public function getAll(): array
    {
        $key = $this->generateRedisKey('all');
        $data = $this->getFromRedis($key);

        if ($data) {
            return $data;
        }

        $model = $this->createModel();
        $this->setRepositoryModel($model);
        $data = $this->repository->findAll();

        $this->storeInRedis($key, $data);
        return $data;
    }

    /**
     * Fetches a record by ID from the repository or Redis cache.
     *
     * @param int $id
     * @return Model|null
     * @throws JsonException
     * @throws Exception
     */
    public function getById(int $id): ?Model
    {
        $key = $this->generateRedisKey($id);
        $data = $this->getFromRedis($key);

        if ($data) {
            return $this->mapToModel($data);
        }

        $model = $this->mapToModel(['id' => $id]);
        $this->setRepositoryModel($model);
        $data = $this->repository->find();

        $this->storeInRedis($key, $data);
        return $data;
    }

    /**
     * Creates a new record in the repository.
     *
     * @param Request $data
     * @return bool
     * @throws Exception
     */
    public function create(Request $data): bool
    {
        if (!$this->validate($data, true)) {
            return false;
        }

        $model = $this->mapToModel($data);
        $this->setRepositoryModel($model);

        return $this->repository->create();
    }

    /**
     * Updates an existing record in the repository.
     *
     * @param Request $data
     * @return bool
     * @throws Exception
     */
    public function update(Request $data): bool
    {
        if (!$this->validate($data, false)) {
            return false;
        }

        $model = $this->createModel();
        $model->fill($data->all());
        $this->setRepositoryModel($model);

        return $this->repository->update();
    }

    /**
     * Deletes a record by ID from the repository.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $model = $this->mapToModel(['id' => $id]);
        $this->setRepositoryModel($model);

        return $this->repository->delete();
    }

    /**
     * Generates a Redis key for caching.
     *
     * @param int|string $suffix
     * @return string
     */
    protected function generateRedisKey(int|string $suffix): string
    {
        return $this->getModelClass() . '_' . $suffix;
    }

    /**
     * Fetches data from Redis.
     *
     * @param string $key
     * @return array|null
     * @throws JsonException
     */
    protected function getFromRedis(string $key): ?array
    {
        $data = $this->redis->get($key);
        return $data ? json_decode($data, true, 512, JSON_THROW_ON_ERROR) : null;
    }

    /**
     * Stores data in Redis.
     *
     * @param string $key
     * @param mixed $data
     * @throws JsonException
     */
    protected function storeInRedis(string $key, mixed $data): void
    {
        $this->redis->setex($key, 3600, json_encode($data, JSON_THROW_ON_ERROR));
    }

    /**
     * Creates a new instance of the model.
     *
     * @param array $attributes
     * @return Model
     */
    protected function createModel(array $attributes = []): Model
    {
        $modelClass = $this->getModelClass();
        return new $modelClass($attributes);
    }

    /**
     * Sets the model in the repository.
     *
     * @param Model $model
     */
    protected function setRepositoryModel(Model $model): void
    {
        $this->repository->setModel($model);
    }

    /**
     * Validates the request data.
     *
     * @param Request $data
     * @param bool $isCreate
     * @return bool
     */
    abstract protected function validate(Request $data, bool $isCreate): bool;

    /**
     * Maps request data or array to a model.
     *
     * @param array|Request $data
     * @return Model
     */
    abstract protected function mapToModel(Request|array $data): Model;

    /**
     * Returns the fully qualified class name of the model.
     *
     * @return string
     */
    abstract protected function getModelClass(): string;
}