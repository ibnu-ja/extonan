<?php

namespace App\Data\Music;

use App\Data\Anime\PostShowData;
use App\Data\MusicSummaryData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MusicShowResponse extends Data
{
    public function __construct(
        public PostShowData $post,
        /** @var DataCollection<int, MusicSummaryData> */
        #[DataCollectionOf(MusicSummaryData::class)]
        public DataCollection $related,
        public bool $canPublish = false,
    ) {}
}
