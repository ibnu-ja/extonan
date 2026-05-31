<?php

namespace App\Data\Anime;

use App\Data\LabelValue;
use App\Data\PaginatedCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AnimeIndexResponse extends Data
{
    public function __construct(
        /** @var PaginatedCollection<AnimeListItemData> */
        public PaginatedCollection $anime,
        /** @var string[] */
        public array $seasons,
        /** @var DataCollection<LabelValue> */
        #[DataCollectionOf(LabelValue::class)]
        public DataCollection $genres,
        /** @var DataCollection<LabelValue> */
        #[DataCollectionOf(LabelValue::class)]
        public DataCollection $tags,
        /** @var DataCollection<LabelValue> */
        #[DataCollectionOf(LabelValue::class)]
        public DataCollection $sortOptions,
        /** @var int[] */
        public array $perPageValues = [],
    ) {}
}
