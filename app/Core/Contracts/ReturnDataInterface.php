<?php

namespace App\Core\Contracts;

interface ReturnDataInterface
{
    public function toData(): \Spatie\LaravelData\Data;
}
