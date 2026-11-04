<?php

namespace App\Data\Music;

use App\Data\Anime\ResourceData;
use App\Data\MediaData;
use App\Enums\ResourceType;
use App\Http\Requests\ShinraiPostType;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ShinraiStoreData extends Data
{
    public function __construct(
        /** @var array<string, string|null> */
        public array $title,
        /** @var array<string, string|null> */
        public array $description = [],
        public string $postType = 'mv',
        public bool $isPublished = false,
        /** @var ResourceData[] */
        public array $links = [],
        public ?ResourceData $embed = null,
        public ?ResourceData $saluran = null,
        public ?MediaData $thumbnailItem = null,
        /** @var array<string, mixed>|null */
        public ?array $vgmdbData = null,
    ) {}

    public function toModelArray(): array
    {
        return [
            'title' => $this->title,
            'description' => ! empty($this->description) ? $this->description : ['id' => null],
            'metadata' => array_filter([
                'post_type' => $this->postType,
                'vgmdb_data' => $this->vgmdbData,
            ], fn ($v) => $v !== null),
            'is_published' => $this->isPublished,
        ];
    }

    public static function rules(?ValidationContext $context): array
    {
        return [
            'title' => 'required|array',
            'title.en' => 'nullable|string|max:255',
            'title.id' => 'nullable|string|max:255',
            'title.romaji' => 'nullable|string|max:255',
            'title.native' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string|max:10000',
            'description.id' => 'nullable|string|max:10000',
            'postType' => ['required', Rule::enum(ShinraiPostType::class)],
            'isPublished' => 'required|boolean',
            'links' => 'nullable|array',
            'links.*.name' => 'required_with:links|string|max:255',
            'links.*.type' => ['required_with:links', Rule::enum(ResourceType::class)],
            'links.*.value' => 'required_with:links|array',
            'links.*.value.*.name' => 'required_with:links.*.value|string',
            'links.*.value.*.value' => 'required_with:links.*.value|string',
            'embed' => 'nullable',
            'saluran' => 'nullable',
            'thumbnailItem' => 'nullable',
            'vgmdbData' => 'nullable|array',
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

        if (! array_key_exists('links', $properties) || $properties['links'] === null) {
            $properties['links'] = [];
        }

        if (! array_key_exists('description', $properties) || $properties['description'] === null) {
            $properties['description'] = [];
        }

        return $properties;
    }
}
