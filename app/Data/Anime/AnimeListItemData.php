<?php

namespace App\Data\Anime;

use App\Data\CoverImageData;
use App\Data\PermissionsData;
use App\Models\Anime;
use App\Models\User;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;

#[TypeScript]
class AnimeListItemData extends Data
{
    public function __construct(
        public int $id,
        /** @var array<string, string|null> */
        public array $title,
        /** @var array<string, string|null> */
        public array $slug,
        public CoverImageData $coverImage,
        public bool $isPublished,
        public string $link,
        public PermissionsData $permissions,
    ) {}

    public static function fromModel(Anime $anime, ?User $user = null): self
    {
        $meta = $anime->metadata;

        return new self(
            id: $anime->id,
            title: $anime->getTranslations('title'),
            slug: $anime->getTranslations('slug'),
            coverImage: new CoverImageData(
                extraLarge: $meta->coverImage->extraLarge ?? '',
                large: $meta->coverImage->large ?? '',
                medium: $meta->coverImage->medium ?? '',
                color: $meta->coverImage->color ?? '',
            ),
            isPublished: $anime->is_published,
            link: route('anime.show', $anime),
            permissions: PermissionsData::fromModel($anime, $user),
        );
    }
}
