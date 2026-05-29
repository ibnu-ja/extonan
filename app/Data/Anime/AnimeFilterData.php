<?php

namespace App\Data\Anime;

use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;

#[TypeScript]
class AnimeFilterData extends Data
{
    public function __construct(
        /** @var string[] */
        public array $genreIn = [],
        /** @var string[] */
        public array $genreNotIn = [],
        /** @var string[] */
        public array $tagIn = [],
        /** @var string[] */
        public array $tagNotIn = [],
        /** @var string[] */
        public array $seasonIn = [],
        /** @var string[] */
        public array $seasonNotIn = [],
        public ?string $title = null,
        public ?bool $isPublished = null,
    ) {}
}
