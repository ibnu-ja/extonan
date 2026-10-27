<?php

namespace App\Data\Anilist;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TitleData extends Data
{
    public function __construct(
        public ?string $romaji = null,
        public ?string $english = null,
        public ?string $native = null,
    ) {}
}
