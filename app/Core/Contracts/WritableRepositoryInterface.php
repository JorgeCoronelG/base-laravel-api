<?php

namespace App\Core\Contracts;

use Illuminate\Database\Eloquent\Model;

interface WritableRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Model;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int|string $id, array $data): Model;

    public function delete(int|string $id): void;
}
