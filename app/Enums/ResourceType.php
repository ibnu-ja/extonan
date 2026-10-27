<?php

namespace App\Enums;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum ResourceType: string
{
    case Link = 'link';
    case Saluran = 'saluran';
    case Embed = 'embed';
}
