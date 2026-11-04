<?php

namespace App\Data\Music;

use App\Data\MusicSummaryData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MusicIndexResponse extends Data
{
    public function __construct(
        /** @var PaginatedDataCollection<int, MusicSummaryData> */
        #[DataCollectionOf(MusicSummaryData::class)]
        public PaginatedDataCollection $mv,
        public bool $canCreate = false,
        public bool $canViewUnpublished = false,
    ) {}
}
