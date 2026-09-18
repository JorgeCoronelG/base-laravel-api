<?php

namespace App\Core;

use App\Core\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $entity) {}

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

    public function delete(int|string $id): void
    {
        $entity = $this->findById($id);
        $entity->delete();
    }

    public function bulkDelete(array $ids, string $primaryKey = 'id'): int
    {
        return $this->entity
            ->whereIn($primaryKey, $ids)
            ->delete();
    }

    public function findAll(
        array $filter = [],
        ?string $sort = null,
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
        ?string $sort = null,
        array $columns = ['*']
    ): LengthAwarePaginator {
        return $this->entity
            ->filter($filters)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }

    public function findById(int|string $id, array $columns = ['*']): Model
    {
        return $this->entity->findOrFail($id, $columns);
    }

    public function findRandom(): Model
    {
        return $this->entity
            ->inRandomOrder()
            ->limit(1)
            ->firstOrFail();
    }

    public function findRandoms(int $records = 1): Collection
    {
        return $this->entity
            ->inRandomOrder()
            ->limit($records)
            ->get();
    }

    public function sync(int|string $id, string $relation, array $attributes, bool $detaching = true): array
    {
        return $this->findById($id)
            ->{$relation}()
            ->sync($attributes, $detaching);
    }

    /**
     * @throws \Throwable
     */
    public function update(int|string $id, array $data): Model
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
