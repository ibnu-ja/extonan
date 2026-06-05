<?php

namespace App\Data\Anilist;

use App\Enums\Season;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AnilistMediaData extends Data
{
    public function __construct(
        public ?int $id = null,
        public ?int $idMal = null,
        public ?CoverImageData $coverImage = null,
        public ?TitleData $title = null,
        public ?FuzzyDateData $startDate = null,
        public ?FuzzyDateData $endDate = null,
        public ?int $episodes = null,
        public ?string $description = null,
        public ?string $bannerImage = null,
        public ?Season $season = null,
        public ?int $seasonYear = null,
        public ?int $seasonInt = null,
        /** @var string[] */
        public array $genres = [],
        /** @var TagData[] */
        public array $tags = [],
        public ?StudioConnectionData $studios = null,
        public ?CharacterConnectionData $characters = null,
    ) {}

    public static function rules(?ValidationContext $context): array
    {
        return [
            'season' => 'nullable|string|in:WINTER,SPRING,SUMMER,FALL',
            'seasonYear' => 'nullable|integer|min:1900|max:2100',
            'genres' => 'nullable|array',
            'genres.*' => 'string|max:255',
            'tags' => 'nullable|array',
        ];
    }
}
