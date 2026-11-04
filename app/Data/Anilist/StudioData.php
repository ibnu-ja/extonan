<?php

namespace App\Data\Anilist;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class StudioData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public bool $isAnimationStudio = false,
    ) {}
}
