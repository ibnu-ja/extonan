<?php

namespace App\Data\Anime;

use App\Data\CoverImageData;
use App\Data\PermissionsData;
use App\Data\UserSummaryData;
use App\Models\Post;
use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class EpisodeShowData extends Data
{
    public function __construct(
        public int $id,
        /** @var array<string, string|null> */
        public array $title,
        public ?string $epNo,
        public string $postType,
        public ?CoverImageData $thumbnail,
        public ?UserSummaryData $author,
        public ?string $publishedAt,
        public bool $isPublished,
        public PermissionsData $permissions,
    ) {}

    public static function fromModel(Post $post, mixed $postable, ?User $user = null): self
    {
        $meta = $post->metadata;
        $thumbnail = $post->thumbnail;

        return new self(
            id: $post->id,
            title: $post->getTranslations('title'),
            epNo: $meta->ep_no ?? null,
            postType: $meta->post_type ?? 'tv',
            thumbnail: $thumbnail ? new CoverImageData(
                extraLarge: $thumbnail['extraLarge'] ?? '',
                large: $thumbnail['large'] ?? '',
                medium: $thumbnail['medium'] ?? '',
                color: $thumbnail['color'] ?? '',
            ) : null,
            author: UserSummaryData::fromModel($post->author),
            publishedAt: $post->published_at?->toIso8601String(),
            isPublished: $post->is_published,
            permissions: PermissionsData::fromModel($post, $user),
        );
    }
}
