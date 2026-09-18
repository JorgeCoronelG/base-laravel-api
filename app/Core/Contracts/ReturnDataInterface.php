<?php

namespace App\Core\Contracts;

use Spatie\LaravelData\Data;

interface ReturnDataInterface
{
    public function toData(): Data|array;
}
