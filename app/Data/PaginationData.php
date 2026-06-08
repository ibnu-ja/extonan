<?php

namespace App\Data;

use Illuminate\Pagination\LengthAwarePaginator;
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

    /**
     * todo check class paginatornya, pakai abstrak atau konkrit paginator
     */
    public static function fromPaginator(LengthAwarePaginator $paginator): self
    {
        return new self(
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
            perPage: $paginator->perPage(),
            total: $paginator->total(),
            links: $paginator->linkCollection()->toArray(),
        );
    }
}
