<?php

namespace App\Data\Anime;

use App\Data\MediaData;
use App\Models\Post;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PostFormData extends Data
{
    public function __construct(
        public ?int $id = null,
        /** @var array<string, string|null> */
        public array $title = [],
        /** @var array<string, string|null> */
        public array $description = [],
        public string $postType = 'tv',
        public ?string $epNo = null,
        public bool $isPublished = false,
        public bool $canPublish = false,
        /** @var ResourceData[] */
        public array $links = [],
        public ?ResourceData $embed = null,
        public ?ResourceData $saluran = null,
        public ?MediaData $thumbnailItem = null,
    ) {}

    public static function fromModel(Post $post, bool $canPublish): self
    {
        $meta = $post->metadata;
        $media = $post->firstMedia('thumbnail');

        return new self(
            id: $post->id,
            title: $post->getTranslations('title'),
            description: $post->getTranslations('description'),
            postType: $meta->post_type ?? 'tv',
            epNo: $meta->ep_no ?? null,
            isPublished: $post->is_published,
            canPublish: $canPublish,
            links: $post->links->map(fn ($r) => ResourceData::fromModel($r))->values()->toArray(),
            embed: $post->embed ? ResourceData::fromModel($post->embed) : null,
            saluran: $post->saluran ? ResourceData::fromModel($post->saluran) : null,
            thumbnailItem: $media ? MediaData::fromModel($media) : null,
        );
    }
}
