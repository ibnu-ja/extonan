<?php

namespace App\Observers;

use App\Models\Post;

class RecordAuthorObserver
{
    /**
     * Sets author when record is created
     */
    public function creating(Post $post): void
    {
        $post->author_id = auth()->id();
    }

    /**
     * Sets the publisher when record is published
     */
    public function updating(Post $post): void
    {
        if (request()->has('is_published') && request()->get('is_published') === true && $post->getOriginal('is_published') === false) {
            $post->publisher_id = auth()->id();
        }
    }
}
