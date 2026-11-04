<?php

namespace App\Data\Anilist;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CoverImageData extends Data
{
    public function __construct(
        public ?string $extraLarge = null,
        public ?string $large = null,
        public ?string $medium = null,
        public ?string $color = null,
    ) {}
}
