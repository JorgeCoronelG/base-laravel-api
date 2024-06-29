<?php

namespace App\Core\Classes;

use App\Core\Enum\OperatorSql;

class Filter
{
    public function __construct(
        public string $field,
        public mixed $value,
        public OperatorSql $operator
    ) {}
}
