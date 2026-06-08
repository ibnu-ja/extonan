<?php

namespace App\Data\Anime;

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
        /** @var array<int, array{id?: int, name: string, type: string, value: array<int, array{name: string, value: string}>}> */
        public array $links = [],
        public ?array $thumbnailItem = null,
    ) {}

    public static function fromModel(Post $post, bool $canPublish): self
    {
        $meta = $post->metadata;

        return new self(
            id: $post->id,
            title: $post->getTranslations('title'),
            description: $post->getTranslations('description'),
            postType: $meta->post_type ?? 'tv',
            epNo: $meta->ep_no ?? null,
            isPublished: $post->is_published,
            canPublish: $canPublish,
            links: $post->links->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'type' => $r->type->value,
                'value' => $r->value,
            ])->values()->toArray(),
            thumbnailItem: $post->firstMedia('thumbnail')?->toArray(),
        );
    }
}
