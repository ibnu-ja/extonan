<?php

namespace App\Data\Anilist;

use App\Enums\CharacterRole;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CharacterEdgeData extends Data
{
    public function __construct(
        public ?CharacterData $node = null,
        public ?CharacterRole $role = null,
        /** @var StaffData[] */
        public array $voiceActors = [],
    ) {}
}
