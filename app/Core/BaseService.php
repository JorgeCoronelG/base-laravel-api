<?php

namespace App\Core;

use App\Core\Classes\ListQuery;
use App\Core\Contracts\BaseRepositoryInterface;
use App\Core\Contracts\BaseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;

class BaseService implements BaseServiceInterface
{
    public function __construct(protected BaseRepositoryInterface $entityRepository)
    {
    }

    public function create(Data $data): Model
    {
        return $this->entityRepository->create($data->toArray());
    }

    public function delete(int|string $id): void
    {
        $this->entityRepository->delete($id);
    }

    public function findAll(
        array $filter = [],
        ?string $sort = null,
        array $columns = ['*']
    ): Collection {
        return $this->entityRepository->findAll($filter, $sort, $columns);
    }

    public function findAllPaginated(ListQuery $query, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->entityRepository->findAllPaginated(
            $query->filters,
            $query->perPage,
            $query->sort,
            $columns
        );
    }

    public function findById(int|string $id, array $columns = ['*']): Model
    {
        return $this->entityRepository->findById($id, $columns);
    }

    public function findRandom(): Model
    {
        return $this->entityRepository->findRandom();
    }

    public function findRandoms(int $records = 1): Collection
    {
        return $this->entityRepository->findRandoms($records);
    }

    public function update(int|string $id, Data $data): Model
    {
        return $this->entityRepository->update($id, $data->toArray());
    }
}
