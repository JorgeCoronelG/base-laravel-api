<?php

namespace Tests\Support;

use App\Core\BaseRepository;

class ItemRepository extends BaseRepository
{
    public function __construct(Item $entity = new Item)
    {
        parent::__construct($entity);
    }
}
