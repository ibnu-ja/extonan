<?php

namespace App\Data\Anime;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

#[TypeScript]
class AnimeAZResponse extends Data
{
    public function __construct(
        /** @var DataCollection<AnimeListItemData> */
        #[DataCollectionOf(AnimeListItemData::class)]
        public DataCollection $items,
        public bool $canCreate,
    ) {}
}
