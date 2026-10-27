<?php

namespace App\Enums;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum CharacterRole: string
{
    case Main = 'MAIN';
    case Supporting = 'SUPPORTING';
    case Background = 'BACKGROUND';
}
