<?php

namespace App\Data\Anime;

use App\Data\PaginationData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

#[TypeScript]
class AnimeIndexResponse extends Data
{
    public function __construct(
        /** @var DataCollection<AnimeListItemData> */
        #[DataCollectionOf(AnimeListItemData::class)]
        public DataCollection $items,
        public PaginationData $pagination,
        /** @var string[] */
        public array $seasons,
        public bool $canCreate,
    ) {}
}
