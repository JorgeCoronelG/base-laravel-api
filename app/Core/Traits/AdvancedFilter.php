<?php

namespace App\Core\Traits;

use App\Core\Classes\Filter;
use App\Core\Enum\OperatorSql;
use Illuminate\Database\Eloquent\Builder;

trait AdvancedFilter
{
    /**
     * @param Builder $query
     * @param Filter[] $filters
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        foreach ($filters as $filter) {
            $this->filterAdvanced($query, $filter);
        }

        return $query;
    }

    private function filterAdvanced(Builder $query, Filter $filter): Builder
    {
        if (OperatorSql::CONTAIN === $filter->operator) {
            $query->orWhere($filter->field, 'LIKE', "%$filter->value%");
            return $query;
        }

        if (OperatorSql::NOT_CONTAIN === $filter->operator) {
            $query->orWhere($filter->field, 'NOT LIKE', "%$filter->value%");
            return $query;
        }

        if (OperatorSql::STARTS_WITH === $filter->operator) {
            $query->orWhere($filter->field, 'LIKE', "$filter->value%");
            return $query;
        }

        if (OperatorSql::ENDS_WITH === $filter->operator) {
            $query->orWhere($filter->field, 'LIKE', "%$filter->value");
            return $query;
        }

        if (OperatorSql::IS_NULL === $filter->operator) {
            $query->orWhereNull($filter->field);
            return $query;
        }

        if (OperatorSql::NOT_NULL === $filter->operator) {
            $query->orWhereNotNull($filter->field);
            return $query;
        }

        if (
            OperatorSql::EQUAL === $filter->operator ||
            OperatorSql::NOT_EQUAL === $filter->operator ||
            OperatorSql::GREATER_THAN === $filter->operator ||
            OperatorSql::GREATER_THAN_OR_EQUAL === $filter->operator ||
            OperatorSql::LESS_THAN === $filter->operator ||
            OperatorSql::LESS_THAN_OR_EQUAL === $filter->operator
        ) {
            $query->orWhere($filter->field, $filter->operator->value, $filter->value);
            return $query;
        }

        return $query;
    }
}
