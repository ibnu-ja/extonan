<?php

namespace App\Data\Anilist;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TagData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?int $rank = null,
        public ?bool $isAdult = null,
        public ?string $category = null,
        public ?bool $isMediaSpoiler = null,
        public ?bool $isGeneralSpoiler = null,
        public ?string $description = null,
    ) {}

    public static function rules(?ValidationContext $context): array
    {
        return [
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
        ];
    }
}
