<?php

namespace App\Core\Classes;

use App\Core\Enum\QueryParam;
use App\Exceptions\CustomErrorException;
use App\Helpers\Validation;
use Illuminate\Http\Request;

/**
 * Parámetros de un listado (filtros, orden y tamaño de página) independientes de HTTP.
 * El controller la construye desde el request y el servicio solo recibe este objeto,
 * así el servicio se puede usar también desde jobs, comandos o tests.
 */
class ListQuery
{
    /**
     * @param Filter[] $filters
     */
    public function __construct(
        public array $filters = [],
        public ?string $sort = null,
        public int $perPage = QueryParam::PAGINATION_ITEMS_DEFAULT
    ) {}

    /**
     * @throws CustomErrorException
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            Validation::getFilters($request->get(QueryParam::FILTERS_KEY)),
            $request->get(QueryParam::ORDER_BY_KEY),
            Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY))
        );
    }
}
