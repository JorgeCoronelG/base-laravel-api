<?php

namespace Tests\Support;

use App\Core\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class ItemRepository extends BaseRepository
{
    public function __construct(Model $entity = new Item())
    {
        $this->entity = $entity;
    }
}
