<?php

namespace App\Core\Contracts;

interface ScopeFilterInterface
{
    public function scopeFilter(\Illuminate\Database\Eloquent\Builder $query, array $params = []): \Illuminate\Database\Eloquent\Builder;
}
