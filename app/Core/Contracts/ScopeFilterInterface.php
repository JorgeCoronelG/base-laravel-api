<?php

namespace App\Core\Contracts;

use App\Core\Classes\Filter;

interface ScopeFilterInterface
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Filter[] $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(\Illuminate\Database\Eloquent\Builder $query, array $filters = []): \Illuminate\Database\Eloquent\Builder;
}
