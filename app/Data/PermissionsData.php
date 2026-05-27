<?php

namespace App\Data;

use App\Models\BasePost;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;

#[TypeScript]
class PermissionsData extends Data
{
    public function __construct(
        public bool $update,
        public bool $delete,
        public bool $publish,
    ) {}

    public static function fromModel(BasePost $post, ?User $user = null): self
    {
        $user ??= Auth::user();

        return new self(
            update: $user?->can('update', $post) ?? false,
            delete: $user?->can('delete', $post) ?? false,
            publish: $user?->can('publish', $post) ?? false,
        );
    }
}
