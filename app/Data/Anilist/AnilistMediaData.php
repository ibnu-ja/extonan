<?php

namespace App\Data\Anilist;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AnilistMediaData extends Data
{
    public function __construct(
        public int $id,
        public ?int $idMal,
        public CoverImageData $coverImage,
        public TitleData $title,
        public FuzzyDateData $startDate,
        public FuzzyDateData $endDate,
        public ?int $episodes,
        public ?string $description,
        public ?string $bannerImage,
        public ?string $season,
        public ?int $seasonYear,
        public ?int $seasonInt,
        /** @var string[] */
        public array $genres,
        /** @var TagData[] */
        public array $tags,
        /** @var array<string, mixed> */
        public ?array $studios,
        /** @var array<string, mixed> */
        public ?array $characters,
    ) {}

    public static function fromResponse(?array $data): ?self
    {
        if ($data === null) {
            return null;
        }

        return new self(
            id: $data['id'],
            idMal: $data['idMal'] ?? null,
            coverImage: CoverImageData::from($data['coverImage'] ?? []),
            title: TitleData::from($data['title'] ?? []),
            startDate: FuzzyDateData::from($data['startDate'] ?? []),
            endDate: FuzzyDateData::from($data['endDate'] ?? []),
            episodes: $data['episodes'] ?? null,
            description: $data['description'] ?? null,
            bannerImage: $data['bannerImage'] ?? null,
            season: $data['season'] ?? null,
            seasonYear: $data['seasonYear'] ?? null,
            seasonInt: $data['seasonInt'] ?? null,
            genres: $data['genres'] ?? [],
            tags: array_map(fn (array $t) => new TagData(
                id: $t['id'],
                name: $t['name'],
                rank: $t['rank'],
                isAdult: $t['isAdult'],
                category: $t['category'],
                isMediaSpoiler: $t['isMediaSpoiler'],
                isGeneralSpoiler: $t['isGeneralSpoiler'],
            ), $data['tags'] ?? []),
            studios: $data['studios'] ?? null,
            characters: $data['characters'] ?? null,
        );
    }
}

#[TypeScript]
class CoverImageData extends Data
{
    public function __construct(
        public string $extraLarge,
        public string $large,
        public string $medium,
        public string $color,
    ) {}
}

#[TypeScript]
class FuzzyDateData extends Data
{
    public function __construct(
        public ?int $year,
        public ?int $month,
        public ?int $day,
    ) {}
}

#[TypeScript]
class TitleData extends Data
{
    public function __construct(
        public ?string $romaji,
        public ?string $english,
        public ?string $native,
    ) {}
}

#[TypeScript]
class TagData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public int $rank,
        public bool $isAdult,
        public string $category,
        public bool $isMediaSpoiler,
        public bool $isGeneralSpoiler,
    ) {}
}
