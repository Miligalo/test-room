<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

abstract class Repository
{
    protected Model $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function create(array $data): Model
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(array $data, int|string $id): bool
    {
        $record = $this->model->newQuery()->find($id);

        return $record->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->model->destroy($id);
    }

    public function one(int $id): ?Model
    {
        return $this->model->newQuery()->find($id);
    }

    public function with($relations, $callback = null): Builder
    {
        return $callback
            ? $this->model->with($relations, $callback)
            : $this->model->with($relations);
    }

    public function updateIn(array $data, array $ids): bool
    {
        return $this->model->newQuery()
            ->whereIn('id', $ids)
            ->update($data) > 0;
    }

    public function deleteIn(array $ids): bool
    {
        return $this->model->newQuery()
            ->whereIn('id', $ids)
            ->delete() > 0;
    }

    protected function addIssetLikeQuery(Builder $query, array $validated, string $field): Builder
    {
        if (isset($validated[$field])) {
            return $query->where("{$query->from}.{$field}", 'ilike', '%' . $validated[$field] . '%');
        }

        return $query;
    }

    protected function addIssetQuery(Builder $query, array $validated, string $field): Builder
    {
        if (isset($validated[$field])) {
            return $query->where("{$query->from}.{$field}", '=', $validated[$field]);
        }

        return $query;
    }

    protected function addBaseSearchParamsToQuery(Builder $builder, array $validated): Builder
    {
        if (isset($validated['dates'])) {
            $builder->whereBetween("{$builder->from}.created_at", $validated['dates']);
        }

        if (isset($validated['id'])) {
            $builder->where("{$builder->from}.id", $validated['id']);
        }

        return $builder;
    }
}
