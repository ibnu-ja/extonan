<?php

namespace App\Data\Anime;

use App\Enums\ResourceType;
use App\Models\Resource;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ResourceData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ResourceType $type,
        /** @var array{name: string, value: string}> */
        public array $value,
    ) {}

    public static function fromModel(Resource $resource): self
    {
        return new self(
            id: $resource->id,
            name: $resource->name,
            type: ResourceType::from($resource->type),
            value: $resource->value,
        );
    }
}
