<?php

namespace Tests\Support;

use App\Core\BaseService;
use App\Core\Contracts\BaseRepositoryInterface;

class ItemService extends BaseService
{
    public function __construct(BaseRepositoryInterface $entityRepository)
    {
        parent::__construct($entityRepository);
    }
}
