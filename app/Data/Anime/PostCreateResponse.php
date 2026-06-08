<?php

namespace App\Data\Anime;

use App\Data\Anilist\AnilistMediaData;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PostCreateResponse extends Data
{
    public function __construct(
        public AnimeListItemData $anime,
        public ?PostFormData $post = null,
        public bool $canPublish = false,
        public ?AnilistMediaData $metadata = null,
    ) {}
}
