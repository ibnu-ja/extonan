<?php

namespace App\Data\Anime;

use App\Data\Anilist\AnilistMediaData;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PostCreateResponse extends Data
{
    public function __construct(
        public AnimeListItemData $anime,
        public PostFormData|Optional $post = new Optional,
        public bool $canPublish = false,
        public ?AnilistMediaData $metadata = null,
    ) {}
}
