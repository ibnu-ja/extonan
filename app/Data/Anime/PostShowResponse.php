<?php

namespace App\Data\Anime;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PostShowResponse extends Data
{
    public function __construct(
        public AnimeListItemData $anime,
        /** @var DataCollection<EpisodeListItemData> */
        #[DataCollectionOf(EpisodeListItemData::class)]
        public DataCollection $episodes,
        public PostShowData $post,
    ) {}
}
