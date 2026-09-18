<?php

namespace App\Core\Contracts;

interface BaseServiceInterface
{
    public function create(\Spatie\LaravelData\Data $data): \Illuminate\Database\Eloquent\Model;

    public function delete(int|string $id): void;

    public function findAll(array $filter = [], ?string $sort = null, array $columns = ['*']): \Illuminate\Database\Eloquent\Collection;

    public function findAllPaginated(\App\Core\Classes\ListQuery $query, array $columns = ['*']): \Illuminate\Pagination\LengthAwarePaginator;

    public function findById(int|string $id, array $columns = ['*']): \Illuminate\Database\Eloquent\Model;

    public function findRandom(): \Illuminate\Database\Eloquent\Model;

    public function findRandoms(int $records = 1): \Illuminate\Database\Eloquent\Collection;

    public function update(int|string $id, \Spatie\LaravelData\Data $data): \Illuminate\Database\Eloquent\Model;
}
