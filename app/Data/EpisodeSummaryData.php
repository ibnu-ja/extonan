<?php

namespace App\Data;

use App\Models\Post;
use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class EpisodeSummaryData extends Data
{
    public function __construct(
        public int $id,
        /** @var array<string, string|null> */
        public array $title,
        /** @var array<string, string|null> */
        public array $slug,
        public ?string $epNo,
        public string $postType,
        public ?CoverImageData $thumbnail,
        /** @var array<string, string|null> */
        public array $animeTitle,
        public ?UserSummaryData $author,
        public ?string $publishedAt,
        public bool $isPublished,
        public bool $isCurrent,
        public string $link,
        public PermissionsData $permissions,
    ) {}

    public static function fromModel(Post $post, ?User $user = null): self
    {
        $meta = $post->metadata;
        $thumbnail = $post->thumbnail;

        return new self(
            id: $post->id,
            title: $post->getTranslations('title'),
            slug: $post->getTranslations('slug'),
            epNo: $meta->ep_no ?? null,
            postType: $meta->post_type ?? 'tv',
            thumbnail: $thumbnail ? new CoverImageData(
                extraLarge: $thumbnail['extraLarge'] ?? '',
                large: $thumbnail['large'] ?? '',
                medium: $thumbnail['medium'] ?? '',
                color: $thumbnail['color'] ?? '',
            ) : null,
            animeTitle: $post->postable?->getTranslations('title') ?? [],
            author: UserSummaryData::fromModel($post->author),
            publishedAt: $post->published_at?->toIso8601String(),
            isPublished: $post->is_published,
            isCurrent: $post->is_current,
            link: route('post.show', [$post->postable, $post]),
            permissions: PermissionsData::fromModel($post, $user),
        );
    }
}
