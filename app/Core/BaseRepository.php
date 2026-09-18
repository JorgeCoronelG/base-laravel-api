<?php

namespace App\Core;

use App\Core\Classes\Filter;
use App\Core\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $entity) {}

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws \Throwable
     */
    public function create(array $data): Model
    {
        $entity = $this->entity->newInstance($data);
        $entity->saveOrFail();

        return $entity;
    }

    /**
     * @param  array<int, array<string, mixed>>  $data
     */
    public function bulkInsert(array $data): bool
    {
        return $this->entity->insert($data);
    }

    public function delete(int|string $id): void
    {
        $entity = $this->findById($id);
        $entity->delete();
    }

    /**
     * @param  array<int, int|string>  $ids
     */
    public function bulkDelete(array $ids, string $primaryKey = 'id'): int
    {
        /** @var int $deleted Filas eliminadas (o marcadas, con SoftDeletes) */
        $deleted = $this->entity
            ->whereIn($primaryKey, $ids)
            ->delete();

        return $deleted;
    }

    /**
     * @param  array<int, Filter>  $filter
     * @param  array<int, string>  $columns
     * @return Collection<int, Model>
     */
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

    /**
     * @param  array<int, Filter>  $filters
     * @param  array<int, string>  $columns
     * @return LengthAwarePaginator<int, Model>
     */
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

    /**
     * @param  array<int, string>  $columns
     */
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

    /**
     * @return Collection<int, Model>
     */
    public function findRandoms(int $records = 1): Collection
    {
        return $this->entity
            ->inRandomOrder()
            ->limit($records)
            ->get();
    }

    /**
     * @param  array<int|string, mixed>  $attributes
     * @return array<string, array<int, int|string>>
     */
    public function sync(int|string $id, string $relation, array $attributes, bool $detaching = true): array
    {
        return $this->findById($id)
            ->{$relation}()
            ->sync($attributes, $detaching);
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws \Throwable
     */
    public function update(int|string $id, array $data): Model
    {
        $entity = $this->findById($id);
        $entity->fill($data);
        $entity->saveOrFail();

        return $entity;
    }

    /**
     * @param  array<int, int|string>  $ids
     * @param  array<string, mixed>  $data
     */
    public function bulkUpdate(array $ids, array $data, string $primaryKey = 'id'): int
    {
        return $this->entity
            ->whereIn($primaryKey, $ids)
            ->update($data);
    }
}
