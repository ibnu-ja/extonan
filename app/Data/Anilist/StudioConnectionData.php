<?php

namespace App\Data\Anilist;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class StudioConnectionData extends Data
{
    public function __construct(
        /** @var StudioEdgeData[] */
        public array $edges = [],
    ) {}
}
