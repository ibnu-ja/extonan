<?php

namespace App\Data\Anilist;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CharacterNameData extends Data
{
    public function __construct(
        public ?string $first = null,
        public ?string $middle = null,
        public ?string $last = null,
        public ?string $full = null,
        public ?string $native = null,
    ) {}
}
