<?php

namespace App\Core;

use App\Core\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class BaseRepository implements BaseRepositoryInterface
{
    protected Builder|Model|QueryBuilder $entity;

    /**
     * @throws \Throwable
     */
    public function create(array $data): Model
    {
        $entity = $this->entity->newInstance($data);
        $entity->saveOrFail();
        return $entity;
    }

    public function bulkInsert(array $data): bool
    {
        return $this->entity->insert($data);
    }

    public function delete(int $id): void
    {
        $entity = $this->findById($id);
        $entity->delete();
    }

    public function bulkDelete(array $ids, string $primaryKey = 'id'): bool
    {
        return $this->entity
            ->whereIn($primaryKey, $ids)
            ->delete();
    }

    public function findAll(
        array $filter = [],
        string $sort = null,
        array $columns = ['*']
    ): Collection {
        return $this->entity
            ->filter($filter)
            ->applySort($sort)
            ->get($columns);
    }

    public function findAllPaginated(
        array $filters,
        int $limit,
        string $sort = null,
        array $columns = ['*']
    ): \Illuminate\Pagination\LengthAwarePaginator {
        return $this->entity
            ->filter($filters)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }

    public function findById(int $id, array $columns = ['*']): Model
    {
        return $this->entity->findOrFail($id, $columns);
    }

    public function findRandom(): Model
    {
        return $this->entity
            ->inRandomOrder()
            ->limit(1)
            ->first();
    }

    public function findRandoms(int $records = 1): Collection
    {
        return $this->entity
            ->inRandomOrder()
            ->limit($records)
            ->get();
    }

    public function sync(int $id, string $relation, array $attributes, bool $detaching = true): array
    {
        return $this->findById($id)
            ->{$relation}()
            ->sync($attributes, $detaching);
    }

    /**
     * @throws \Throwable
     */
    public function update(int $id, array $data): Model
    {
        $entity = $this->findById($id);
        $entity->fill($data);
        $entity->saveOrFail();
        return $entity;
    }

    public function bulkUpdate(array $ids, array $data, string $primaryKey = 'id'): int
    {
        return $this->entity
            ->whereIn($primaryKey, $ids)
            ->update($data);
    }
}
