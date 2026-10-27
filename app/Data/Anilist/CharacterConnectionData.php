<?php

namespace App\Data\Anilist;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CharacterConnectionData extends Data
{
    public function __construct(
        /** @var CharacterEdgeData[] */
        public array $edges = [],
    ) {}
}
