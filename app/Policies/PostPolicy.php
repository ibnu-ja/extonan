<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\BasePost;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, BasePost $post): bool
    {
        if ($user?->can(Permission::POST_READ_ANY->value) || $post->isPublished()) {
            return true;
        }

        if ($user?->can(Permission::POST_READ_SELF->value) && $user?->id === $post->author_id && $post->isPublished() === false) {
            return true;
        }

        return $post->isPublished();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(Permission::POST_CREATE->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BasePost $post): bool
    {
        if ($user->can(Permission::POST_UPDATE_ANY->value)) {
            return true;
        }

        return $user->can(Permission::POST_UPDATE_SELF->value) && $user->id === $post->author_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BasePost $post): bool
    {
        if ($user->can(Permission::POST_DELETE_ANY->value)) {
            return true;
        }

        return $user->can(Permission::POST_DELETE_SELF->value) && $user->id === $post->author_id;
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, ?BasePost $post = null): bool
    {
        // if post is null means it's creating and self-publish
        if ($post == null && $user->can(Permission::POST_PUBLISH_SELF->value) || $user->can(Permission::POST_PUBLISH_ANY->value)) {
            return true;
        }

        // when editing user should only able to publish its own post
        return $user->id === $post?->author_id;
    }

    // /**
    // * Determine whether the user can restore the model.
    // */
    // public function restore(User $user, BasePost $post): bool
    // {
    //    //
    // }
    //
    // /**
    // * Determine whether the user can permanently delete the model.
    // */
    // public function forceDelete(User $user, BasePost $post): bool
    // {
    //    //
    // }
}
