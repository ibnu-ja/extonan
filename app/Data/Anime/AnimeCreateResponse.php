<?php

namespace App\Data\Anime;

use App\Data\LabelValue;
use App\Data\TagItem;
use App\Enums\Season;
use Spatie\LaravelData\Attributes\AutoClosureLazy;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AnimeCreateResponse extends Data
{
    public function __construct(
        /** @var Lazy|DataCollection<LabelValue> */
        #[AutoClosureLazy]
        #[DataCollectionOf(LabelValue::class)]
        public Lazy|DataCollection $genres,
        /** @var Lazy|DataCollection<TagItem> */
        #[AutoClosureLazy]
        #[DataCollectionOf(TagItem::class)]
        public Lazy|DataCollection $tags,
        /** @var Lazy|Season[] */
        #[AutoClosureLazy]
        public Lazy|array $seasons,
        #[AutoClosureLazy]
        public Lazy|string $anilistQuery,
        public AnimeFormData|Optional $anime = new Optional,
    ) {}
}
