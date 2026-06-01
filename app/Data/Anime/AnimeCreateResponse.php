<?php

namespace App\Data\Anime;

use App\Data\LabelValue;
use App\Data\TagItem;
use App\Enums\Season;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AnimeCreateResponse extends Data
{
    public function __construct(
        public ?AnimeFormData $anime,
        /** @var DataCollection<LabelValue> */
        #[DataCollectionOf(LabelValue::class)]
        public DataCollection $genres,
        /** @var DataCollection<TagItem> */
        #[DataCollectionOf(TagItem::class)]
        public DataCollection $tags,
        /** @var Season[] */
        public array $seasons,
        public string $anilistQuery,
    ) {}
}
