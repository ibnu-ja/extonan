<?php

namespace App\Data\Anime;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

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
