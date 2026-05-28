<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;

#[TypeScript]
class LabelValue extends Data
{
    public function __construct(
        public string $key,
        public string $value,
    ) {}
}
