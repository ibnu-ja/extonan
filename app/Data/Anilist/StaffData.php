<?php

namespace App\Data\Anilist;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class StaffData extends Data
{
    public function __construct(
        public int $id,
        public ?StaffNameData $name = null,
        public ?StaffImageData $image = null,
        public ?string $languageV2 = null,
    ) {}
}
