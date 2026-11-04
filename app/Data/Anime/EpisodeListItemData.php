<?php

namespace App\Data\Anime;

use App\Data\CoverImageData;
use App\Models\Post;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class EpisodeListItemData extends Data
{
    public function __construct(
        public int $id,
        /** @var array<string, string|null> */
        public array $title,
        /** @var array<string, string|null> */
        public array $slug,
        public ?string $epNo,
        public ?CoverImageData $thumbnail,
        public ?string $publishedAt,
    ) {}

    public static function fromModel(Post $post): self
    {
        $meta = $post->metadata;
        $thumbnail = $post->thumbnail;

        return new self(
            id: $post->id,
            title: $post->getTranslations('title'),
            slug: $post->getTranslations('slug'),
            epNo: $meta->ep_no ?? null,
            thumbnail: $thumbnail ? new CoverImageData(
                extraLarge: $thumbnail['extraLarge'] ?? '',
                large: $thumbnail['large'] ?? '',
                medium: $thumbnail['medium'] ?? '',
                color: $thumbnail['color'] ?? '',
            ) : null,
            publishedAt: $post->published_at?->toIso8601String(),
        );
    }
}
