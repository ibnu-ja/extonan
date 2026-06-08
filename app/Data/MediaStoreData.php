<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class MediaStoreData extends Data
{
    public function __construct(
        public ?array $media,
        public ?array $url,
    ) {}

    public static function rules(): array
    {
        return [
            'media' => 'array|required_without:url|nullable',
            'media.*' => 'file|image|max:10000',
            'url' => 'array|required_without:media|nullable',
            'url.*' => 'string',
        ];
    }
}
