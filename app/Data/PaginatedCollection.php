<?php

namespace App\Data;

use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;

/**
 * @template TValue
 */
#[TypeScript]
class PaginatedCollection extends Data
{
    public function __construct(
        /** @var TValue[] */
        public array $data,
        public int $currentPage,
        public int $lastPage,
        public int $perPage,
        public int $total,
        /** @var array<array{url: string|null, label: string, active: bool}> */
        public array $links,
    ) {}

    /**
     * @param  LengthAwarePaginator<mixed, mixed>  $paginator
     * @param  callable(mixed): TValue  $mapper
     * @return self<TValue>
     */
    public static function fromPaginator(LengthAwarePaginator $paginator, callable $mapper): self
    {
        return new self(
            data: array_map($mapper, $paginator->items()),
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
            perPage: $paginator->perPage(),
            total: $paginator->total(),
            links: $paginator->linkCollection()->toArray(),
        );
    }
}
