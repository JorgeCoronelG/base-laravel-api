<?php

namespace App\Core\Classes;

use App\Core\Enum\OperatorSql;

class Filter
{
    /**
     * @param string $boolean Cómo se une con el filtro anterior: 'and' (por defecto) u 'or'
     */
    public function __construct(
        public string $field,
        public mixed $value,
        public OperatorSql $operator,
        public string $boolean = 'and'
    ) {}
}
