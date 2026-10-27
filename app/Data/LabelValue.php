<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class LabelValue extends Data
{
    public function __construct(
        public string $key,
        public string $value,
    ) {}
}
