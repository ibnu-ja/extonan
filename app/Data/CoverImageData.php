<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;

#[TypeScript]
class CoverImageData extends Data
{
    public function __construct(
        public string $extraLarge,
        public string $large,
        public string $medium,
        public string $color,
    ) {}
}
