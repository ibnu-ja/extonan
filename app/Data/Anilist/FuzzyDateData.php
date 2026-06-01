<?php

namespace App\Data\Anilist;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class FuzzyDateData extends Data
{
    public function __construct(
        public ?int $year = null,
        public ?int $month = null,
        public ?int $day = null,
    ) {}
}
