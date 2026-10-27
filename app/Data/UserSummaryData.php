<?php

namespace App\Data;

use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UserSummaryData extends Data
{
    public function __construct(
        public int $id,
        public ?string $name,
        public ?string $avatar,
    ) {}

    public static function fromModel(?User $user): ?self
    {
        if ($user === null) {
            return null;
        }

        return new self(
            id: $user->id,
            name: $user->name,
            avatar: $user->profile_photo_url ?? $user->profile_photo_path,
        );
    }
}
