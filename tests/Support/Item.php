<?php

namespace Tests\Support;

use App\Core\Traits\AdvancedFilter;
use App\Core\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use AdvancedFilter, Sortable;

    protected $table = 'items';

    protected $fillable = ['name', 'status'];

    public array $allowedSorts = ['id', 'name', 'status'];

    public array $allowedFilters = ['id', 'name', 'status'];
}
