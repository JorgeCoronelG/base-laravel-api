<?php

namespace App\Core\Contracts;

/**
 * Operaciones masivas. No disparan eventos de Eloquent y bulkInsert no rellena timestamps.
 */
interface BulkRepositoryInterface
{
    public function bulkInsert(array $data): bool;

    public function bulkUpdate(array $ids, array $data, string $primaryKey = 'id'): int;

    public function bulkDelete(array $ids, string $primaryKey = 'id'): int;
}
