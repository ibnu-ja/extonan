<?php

namespace App\Policies;

use App\Models\BasePost;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, BasePost $post): bool
    {
        if ($user?->can('post.read.any') || $post->isPublished()) {
            return true;
        }

        if ($user?->can('post.read.self') && $user?->id === $post->author->id && $post->isPublished() === false) {
            return true;
        }

        return $post->isPublished();
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
    public function update(User $user, BasePost $post): bool
    {
        if ($user->can('post.update.any')) {
            return true;
        }

        return $user->can('post.update.self') && $user->id === $post->author->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BasePost $post): bool
    {
        if ($user->can('post.delete.any')) {
            return true;
        }

        return $user->can('post.delete.self') && $user->id === $post->author->id;
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, BasePost $post = null): bool
    {
        //if post is null means it's creating and self-publish
        if ($post == null && $user->can('post.publish.self') || $user->can('post.publish.any')) {
            return true;
        }
        //when editing user should only able to publish its own post
        return $user->id === $post->author->id;
    }

    ///**
    // * Determine whether the user can restore the model.
    // */
    //public function restore(User $user, BasePost $post): bool
    //{
    //    //
    //}
    //
    ///**
    // * Determine whether the user can permanently delete the model.
    // */
    //public function forceDelete(User $user, BasePost $post): bool
    //{
    //    //
    //}
}
