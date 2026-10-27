<?php

namespace App\Data\Anime;

use App\Data\LabelValue;
use App\Data\TagItem;
use Spatie\LaravelData\Attributes\AutoClosureLazy;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AnimeIndexResponse extends Data
{
    public function __construct(
        /** @var PaginatedDataCollection<int, AnimeListItemData> */
        public PaginatedDataCollection $anime,
        /** @var Lazy|string[] */
        #[AutoClosureLazy]
        public Lazy|array $seasons,
        /** @var Lazy|DataCollection<LabelValue> */
        #[AutoClosureLazy]
        #[DataCollectionOf(LabelValue::class)]
        public Lazy|DataCollection $genres,
        /** @var Lazy|DataCollection<TagItem> */
        #[AutoClosureLazy]
        #[DataCollectionOf(TagItem::class)]
        public Lazy|DataCollection $tags,
        /** @var Lazy|DataCollection<LabelValue> */
        #[AutoClosureLazy]
        #[DataCollectionOf(LabelValue::class)]
        public Lazy|DataCollection $sortOptions,
        /** @var int[] */
        public array $perPageValues = [],
    ) {}
}
