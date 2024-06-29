<?php

namespace App\Helpers;

use App\Core\Classes\Filter;
use App\Core\Enum\Message;
use App\Core\Enum\OperatorSql;
use App\Core\Enum\QueryParam;
use App\Exceptions\CustomErrorException;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class Validation
{
    public static function getPerPage(string $queryParam = null): int
    {
        if (is_null($queryParam)) {
            return QueryParam::PAGINATION_ITEMS_DEFAULT;
        }

        return (intval($queryParam) > 0) ? intval($queryParam) : QueryParam::PAGINATION_ITEMS_DEFAULT;
    }

    /**
     * @throws CustomErrorException
     * @return Filter[]
     */
    public static function getFilters(string $queryParam = null): array
    {
        if (is_null($queryParam)) {
            return [];
        }

        $json = urldecode($queryParam);
        $filters = json_decode($json, true);

        if (!isset($filters[QueryParam::FILTERS_FIELD_KEY])) {
            throw new CustomErrorException(Message::INVALID_QUERY_PARAMETER,Response::HTTP_BAD_REQUEST);
        }

        $arrayFilters = [];
        foreach ($filters[QueryParam::FILTERS_FIELD_KEY] as $filter) {
            if (
                !isset($filter[QueryParam::FIELD_KEY]) ||
                !isset($filter[QueryParam::OPERATOR_SQL_KEY])
            ) {
                throw new CustomErrorException(Message::INVALID_QUERY_PARAMETER, Response::HTTP_BAD_REQUEST);
            }

            $arrayFilters[] = new Filter(
                $filter[QueryParam::FIELD_KEY],
                $filter[QueryParam::VALUE_KEY],
                OperatorSql::from($filter[QueryParam::OPERATOR_SQL_KEY])
            );
        }

        return $arrayFilters;
    }

    /**
     * Función para validar una fecha en formato AAAA/MM/DD
     * @throws CustomErrorException
     */
    public static function validateDate(string $date = null): string | null
    {
        if (is_null($date)) {
            throw new CustomErrorException(Message::INVALID_QUERY_PARAMETER, Response::HTTP_BAD_REQUEST);
        }

        $dateParse = null;

        if (Str::of($date)->contains('/')) {
            $dateParse = explode('/', $date);
        }

        if (Str::of($date)->contains('-')) {
            $dateParse = explode('-', $date);
        }

        if (is_null($dateParse)) {
            throw new CustomErrorException(Message::INVALID_QUERY_PARAMETER, Response::HTTP_BAD_REQUEST);
        }

        if (count($dateParse) !== 3 && !checkdate($dateParse[1], $dateParse[2], $dateParse[0])) {
            throw new CustomErrorException(Message::INVALID_QUERY_PARAMETER, Response::HTTP_BAD_REQUEST);
        }

        return $date;
    }
}
