<?php

namespace App\Core\Contracts;

use Illuminate\Database\Eloquent\Model;

interface WritableRepositoryInterface
{
    public function create(array $data): Model;

    public function update(int|string $id, array $data): Model;

    public function delete(int|string $id): void;
}
