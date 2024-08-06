<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        if ($user->can('post.read.any') || $post->isPublished()) {
            return true;
        }

        return $user->can('post.read.self') && $user->id === $post->author->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('post.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        if ($user->can('post.update.any')) {
            return true;
        }

        return $user->can('post.update.self') && $user->id === $post->author->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        if ($user->can('post.delete.any')) {
            return true;
        }

        return $user->can('post.delete.self') && $user->id === $post->author->id;
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Post $post = null): bool
    {
        if ($post == null && $user->can('post.publish.self') || $user->can('post.publish.any')) {
            return true;
        }

        return $user->id === $post->author->id;
    }

//    /**
//     * Determine whether the user can restore the model.
//     */
//    public function restore(User $user, Post $post): bool
//    {
//        //
//    }
//
//    /**
//     * Determine whether the user can permanently delete the model.
//     */
//    public function forceDelete(User $user, Post $post): bool
//    {
//        //
//    }
}
