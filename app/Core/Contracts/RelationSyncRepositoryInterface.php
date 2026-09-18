<?php

namespace App\Core\Contracts;

interface RelationSyncRepositoryInterface
{
    public function sync(int|string $id, string $relation, array $attributes, bool $detaching = true): array;
}
