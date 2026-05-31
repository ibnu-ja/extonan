<?php

namespace App\Data\Anime;

use App\Models\Anime;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AnimeFormData extends Data
{
    public function __construct(
        public ?int $id,
        /** @var array<string, string|null> */
        public array $title,
        /** @var array<string, string|null> */
        public array $description,
        public ?int $anilistId,
        public ?object $metadata,
        public bool $isPublished,
        public bool $canPublish,
    ) {}

    public static function fromModel(Anime $anime, bool $canPublish): self
    {
        return new self(
            id: $anime->id,
            title: $anime->getTranslations('title'),
            description: $anime->getTranslations('description'),
            anilistId: $anime->anilist_id,
            metadata: $anime->metadata,
            isPublished: $anime->is_published,
            canPublish: $canPublish,
        );
    }
}
