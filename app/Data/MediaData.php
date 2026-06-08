<?php

namespace App\Data;

use Plank\Mediable\Media;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MediaData extends Data
{
    public function __construct(
        public int $id,
        public string $filename,
        public string $extension,
        public string $directory,
        public string $url,
        public ?string $mediumUrl,
        public ?string $largeUrl,
        public int $size,
        public string $mimeType,
        public string $aggregateType,
        public string $createdAt,
    ) {}

    public static function fromModel(Media $media): self
    {
        return new self(
            id: $media->id,
            filename: $media->filename,
            extension: $media->extension,
            directory: $media->directory,
            url: $media->getUrl(),
            mediumUrl: $media->findVariant('medium')?->getUrl(),
            largeUrl: $media->findVariant('large')?->getUrl(),
            size: $media->size,
            mimeType: $media->mime_type,
            aggregateType: $media->aggregate_type,
            createdAt: $media->created_at->toIso8601String(),
        );
    }
}
