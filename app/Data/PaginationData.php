<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;

#[TypeScript]
class PaginationData extends Data
{
    public function __construct(
        public int $currentPage,
        public int $lastPage,
        public int $perPage,
        public int $total,
        /** @var array<array{url: string|null, label: string, active: bool}> */
        public array $links,
    ) {}
}
