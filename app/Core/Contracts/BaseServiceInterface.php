<?php

namespace App\Core\Contracts;

use App\Core\Classes\ListQuery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;

interface BaseServiceInterface
{
    public function create(Data $data): Model;

    public function delete(int|string $id): void;

    public function findAll(array $filter = [], ?string $sort = null, array $columns = ['*']): Collection;

    public function findAllPaginated(ListQuery $query, array $columns = ['*']): LengthAwarePaginator;

    public function findById(int|string $id, array $columns = ['*']): Model;

    public function findRandom(): Model;

    public function findRandoms(int $records = 1): Collection;

    public function update(int|string $id, Data $data): Model;
}
