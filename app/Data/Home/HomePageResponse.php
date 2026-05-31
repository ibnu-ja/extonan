<?php

namespace App\Data\Home;

use App\Data\AnimeSummaryData;
use App\Data\EpisodeSummaryData;
use App\Data\MusicSummaryData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class HomePageResponse extends Data
{
    public function __construct(
        /** @var DataCollection<AnimeSummaryData> */
        #[DataCollectionOf(AnimeSummaryData::class)]
        public DataCollection $latestAnime,
        /** @var DataCollection<EpisodeSummaryData> */
        #[DataCollectionOf(EpisodeSummaryData::class)]
        public DataCollection $latestEpisodes,
        /** @var DataCollection<MusicSummaryData> */
        #[DataCollectionOf(MusicSummaryData::class)]
        public DataCollection $latestMv,
        /** @var DataCollection<MusicSummaryData> */
        #[DataCollectionOf(MusicSummaryData::class)]
        public DataCollection $latestAlbum,
    ) {}
}
