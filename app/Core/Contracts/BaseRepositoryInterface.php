<?php

namespace App\Core\Contracts;

/**
 * Contrato completo. Las capas que solo necesiten una parte pueden depender
 * de la interfaz específica (lectura, escritura, masivas o sincronización).
 */
interface BaseRepositoryInterface extends BulkRepositoryInterface, ReadableRepositoryInterface, RelationSyncRepositoryInterface, WritableRepositoryInterface {}
