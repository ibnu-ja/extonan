<?php

namespace App\Data;

use App\Models\Anime;
use App\Models\User;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;

#[TypeScript]
class AnimeSummaryData extends Data
{
    public function __construct(
        public int $id,
        /** @var array<string, string|null> */
        public array $title,
        /** @var array<string, string|null> */
        public array $slug,
        /** @var string[] */
        public array $genres,
        public ?string $bannerImage,
        public CoverImageData $coverImage,
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
            genres: $meta->genres ?? [],
            bannerImage: $meta->bannerImage ?? null,
            coverImage: new CoverImageData(
                extraLarge: $meta->coverImage->extraLarge ?? '',
                large: $meta->coverImage->large ?? '',
                medium: $meta->coverImage->medium ?? '',
                color: $meta->coverImage->color ?? '',
            ),
            link: route('anime.show', $anime),
            permissions: PermissionsData::fromModel($anime, $user),
        );
    }
}
