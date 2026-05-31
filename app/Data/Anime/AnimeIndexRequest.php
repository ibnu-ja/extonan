<?php

namespace App\Data\Anime;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AnimeIndexRequest extends Data
{
    public const array PER_PAGE_VALUES = [14, 25, 50, 100];

    public function __construct(
        public AnimeFilterData $filter = new AnimeFilterData,
        public ?string $sort = null,
        public ?int $perPage = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $perPage = (int) ($properties['perPage'] ?? 0);

        if (! in_array($perPage, self::PER_PAGE_VALUES, true)) {
            $properties['perPage'] = 14;
        }

        return $properties;
    }

    public static function rules(ValidationContext $context): array
    {
        $validGenres = config('anime.genres', []);
        $validTags = config('anime.tags', []);

        return [
            'filter.genreIn' => ['nullable', 'array'],
            'filter.genreIn.*' => ['nullable', 'string', 'in:'.implode(',', $validGenres)],
            'filter.genreNotIn' => ['nullable', 'array'],
            'filter.genreNotIn.*' => ['nullable', 'string', 'in:'.implode(',', $validGenres)],
            'filter.tagIn' => ['nullable', 'array'],
            'filter.tagIn.*' => ['nullable', 'string', 'in:'.implode(',', $validTags)],
            'filter.tagNotIn' => ['nullable', 'array'],
            'filter.tagNotIn.*' => ['nullable', 'string', 'in:'.implode(',', $validTags)],
            'filter.seasonIn' => ['nullable', 'array'],
            'filter.seasonIn.*' => ['nullable', 'string'],
            'filter.seasonNotIn' => ['nullable', 'array'],
            'filter.seasonNotIn.*' => ['nullable', 'string'],
            'filter.title' => ['nullable', 'string', 'max:255'],
            'filter.isPublished' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'string', 'in:title->romaji,-title->romaji,created_at,-created_at,updated_at,-updated_at'],
            'perPage' => ['nullable', 'integer'],
        ];
    }
}
