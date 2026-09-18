<?php

namespace App\Core\Contracts;

use App\Core\Classes\Filter;
use Illuminate\Database\Eloquent\Builder;

interface ScopeFilterInterface
{
    /**
     * @param  Filter[]  $filters
     */
    public function scopeFilter(Builder $query, array $filters = []): Builder;
}
