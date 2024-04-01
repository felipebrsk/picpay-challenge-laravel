<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\{Model, Collection};

abstract class AbstractRepository
{
    /**
     * The abstract model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->model = $this->resolve();
    }

    /**
     * Get all model records.
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->orderByDesc('created_at')->get($columns);
    }

    /**
     * Create a model record.
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Find a model record.
     *
     * @param mixed $id
     * @return ?\Illuminate\Database\Eloquent\Model
     */
    public function find(mixed $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Find a model collection where in array.
     *
     * @param array $ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findIn(array $ids): Collection
    {
        return $this->model->whereIn('id', $ids)->get();
    }

    /**
     * Find or fail a model record.
     *
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrFail(mixed $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Update a model record.
     *
     * @param array $data
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(array $data, mixed $id): Model
    {
        $model = $this->findOrFail($id);

        $model->update($data);

        return $model;
    }

    /**
     * Update or create the model record.
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateOrCreate(array $data): Model
    {
        return $this->model->updateOrCreate($data);
    }

    /**
     * Delete the model record.
     *
     * @param mixed $id
     * @return void
     */
    public function delete(mixed $id): void
    {
        $model = $this->findOrFail($id);

        $model->delete();
    }

    /**
     * Resolve the model instance.
     *
     * @return object
     */
    public function resolve(): object
    {
        return resolve($this->model);
    }
}
