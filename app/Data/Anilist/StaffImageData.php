<?php

namespace App\Data\Anilist;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class StaffImageData extends Data
{
    public function __construct(
        public ?string $large = null,
        public ?string $medium = null,
    ) {}
}
