<?php

namespace App\Data\Anime;

use App\Data\EpisodeSummaryData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AnimeShowResponse extends Data
{
    public function __construct(
        public AnimeListItemData $anime,
        /** @var DataCollection<EpisodeSummaryData> */
        #[DataCollectionOf(EpisodeSummaryData::class)]
        public DataCollection $episodes,
        public bool $canCreateEpisode,
    ) {}
}
