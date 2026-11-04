<?php

namespace App\Data\Music;

use App\Data\Anime\PostFormData;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ShinraiCreateResponse extends Data
{
    public function __construct(
        public string $type = 'mv',
        public PostFormData|Optional $post = new Optional,
        public bool $canPublish = false,
    ) {}
}
