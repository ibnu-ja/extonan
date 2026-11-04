<?php

namespace App\Data\Anilist;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class StudioEdgeData extends Data
{
    public function __construct(
        public ?StudioData $node = null,
        public bool $isMain = false,
    ) {}
}
