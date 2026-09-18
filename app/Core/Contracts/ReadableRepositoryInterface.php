<?php

namespace App\Core\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReadableRepositoryInterface
{
    public function findAll(array $filter = [], ?string $sort = null, array $columns = ['*']): Collection;

    public function findAllPaginated(
        array $filters,
        int $limit,
        ?string $sort = null,
        array $columns = ['*']
    ): LengthAwarePaginator;

    public function findById(int|string $id, array $columns = ['*']): Model;

    public function findRandom(): Model;

    public function findRandoms(int $records = 1): Collection;
}
