<?php

namespace App\Data\Anime;

use App\Data\CoverImageData;
use App\Data\PermissionsData;
use App\Data\UserSummaryData;
use App\Models\Post;
use App\Models\User;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PostShowData extends Data
{
    public function __construct(
        public int $id,
        /** @var array<string, string|null> */
        public array $title,
        /** @var array<string, string|null> */
        public array $description,
        /** @var array<string, string|null> */
        public array $slug,
        public ?string $epNo,
        public string $postType,
        public ?CoverImageData $thumbnail,
        public ?UserSummaryData $author,
        public ?string $publishedAt,
        public bool $isPublished,
        public PermissionsData $permissions,
        /** @var DataCollection<ResourceData> */
        #[DataCollectionOf(ResourceData::class)]
        public DataCollection $links,
        /** @var DataCollection<ResourceData> */
        #[DataCollectionOf(ResourceData::class)]
        public DataCollection $saluran,
        public ?ResourceData $embed = null,
    ) {}

    public static function fromModel(Post $post, ?User $user = null): self
    {
        $meta = $post->metadata;
        $thumbnail = $post->thumbnail;

        return new self(
            id: $post->id,
            title: $post->getTranslations('title'),
            description: $post->getTranslations('description'),
            slug: $post->getTranslations('slug'),
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
            links: new DataCollection(ResourceData::class, $post->links->sortBy('name')->map(
                fn ($r) => ResourceData::fromModel($r),
            )),
            saluran: new DataCollection(ResourceData::class, $post->saluran->map(
                fn ($r) => ResourceData::fromModel($r),
            )),
            embed: $post->embeds->first()
                ? ResourceData::fromModel($post->embeds->first())
                : null,
        );
    }
}
