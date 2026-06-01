<?php

namespace App\Data\Anime;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AnimeStoreData extends Data
{
    public function __construct(
        /** @var array<string, string|null> */
        public array $title,
        /** @var array<string, string|null> */
        public array $description,
        public ?int $anilistId,
        /** @var array<string, mixed>|null */
        public ?array $metadata,
        public bool $isPublished,
    ) {}

    /** @return array<string, mixed> */
    public function toModelArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'anilist_id' => $this->anilistId,
            'metadata' => $this->metadata ?? [],
            'is_published' => $this->isPublished,
        ];
    }

    public static function rules(?ValidationContext $context): array
    {
        return [
            'title' => 'required|array',
            'title.en' => 'nullable|string|max:255',
            'title.id' => 'nullable|string|max:255',
            'title.romaji' => 'required|string|max:255',
            'title.native' => 'required|string|max:255',
            'description' => 'required|array',
            'description.en' => 'nullable|string|max:10000',
            'description.id' => 'nullable|string|max:10000',
            'anilistId' => 'nullable|integer|min:1',
            'metadata' => 'nullable|array',
            'metadata.season' => 'nullable|string|in:WINTER,SPRING,SUMMER,FALL',
            'metadata.seasonYear' => 'nullable|integer|min:1900|max:2100',
            'isPublished' => 'required|boolean',
        ];
    }

    /** @param array<string, mixed> $properties */
    public static function prepareForPipeline(array $properties): array
    {
        if (array_key_exists('isPublished', $properties)) {
            $properties['isPublished'] = filter_var(
                $properties['isPublished'],
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            ) ?? false;
        }

        return $properties;
    }
}
