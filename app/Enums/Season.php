<?php

namespace App\Enums;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum Season: string
{
    case Winter = 'WINTER';
    case Spring = 'SPRING';
    case Summer = 'SUMMER';
    case Fall = 'FALL';
}
