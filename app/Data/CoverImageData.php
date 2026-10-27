<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

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
