<?php

namespace App\Data\Anilist;

use App\Enums\CharacterRole;
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
            season: Season::tryFrom($data['season'] ?? ''),
            seasonYear: $data['seasonYear'] ?? null,
            seasonInt: $data['seasonInt'] ?? null,
            genres: $data['genres'] ?? [],
            tags: array_map(fn (array $t) => new TagData(
                id: $t['id'],
                name: $t['name'],
                rank: $t['rank'] ?? null,
                isAdult: $t['isAdult'] ?? null,
                category: $t['category'] ?? null,
                isMediaSpoiler: $t['isMediaSpoiler'] ?? null,
                isGeneralSpoiler: $t['isGeneralSpoiler'] ?? null,
                description: $t['description'] ?? null,
            ), $data['tags'] ?? []),
            studios: isset($data['studios']) ? StudioConnectionData::from([
                'edges' => array_map(fn (array $edge) => StudioEdgeData::from([
                    'node' => StudioData::from($edge['node']),
                    'isMain' => $edge['isMain'] ?? false,
                ]), $data['studios']['edges'] ?? []),
            ]) : null,
            characters: isset($data['characters']) ? CharacterConnectionData::from([
                'edges' => array_map(fn (array $edge) => CharacterEdgeData::from([
                    'node' => CharacterData::from([
                        'id' => $edge['node']['id'],
                        'name' => CharacterNameData::from($edge['node']['name'] ?? []),
                        'image' => CharacterImageData::from($edge['node']['image'] ?? []),
                    ]),
                    'role' => CharacterRole::tryFrom($edge['role'] ?? ''),
                    'voiceActors' => array_map(fn (array $va) => StaffData::from([
                        'id' => $va['id'],
                        'name' => StaffNameData::from($va['name'] ?? []),
                        'image' => StaffImageData::from($va['image'] ?? []),
                        'languageV2' => $va['languageV2'] ?? null,
                    ]), $edge['voiceActors'] ?? []),
                ]), $data['characters']['edges'] ?? []),
            ]) : null,
        );
    }
}
