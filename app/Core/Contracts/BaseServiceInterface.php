<?php

namespace App\Core\Contracts;

use App\Core\Classes\Filter;
use App\Core\Classes\ListQuery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;

interface BaseServiceInterface
{
    public function create(Data $data): Model;

    public function delete(int|string $id): void;

    /**
     * @param  array<int, Filter>  $filter
     * @param  array<int, string>  $columns
     * @return Collection<int, Model>
     */
    public function findAll(array $filter = [], ?string $sort = null, array $columns = ['*']): Collection;

    /**
     * @param  array<int, string>  $columns
     * @return LengthAwarePaginator<int, Model>
     */
    public function findAllPaginated(ListQuery $query, array $columns = ['*']): LengthAwarePaginator;

    /**
     * @param  array<int, string>  $columns
     */
    public function findById(int|string $id, array $columns = ['*']): Model;

    public function findRandom(): Model;

    /**
     * @return Collection<int, Model>
     */
    public function findRandoms(int $records = 1): Collection;

    public function update(int|string $id, Data $data): Model;
}
