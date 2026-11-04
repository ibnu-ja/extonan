<?php

namespace App\Data\Anilist;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CharacterData extends Data
{
    public function __construct(
        public int $id,
        public ?CharacterNameData $name = null,
        public ?CharacterImageData $image = null,
    ) {}
}
