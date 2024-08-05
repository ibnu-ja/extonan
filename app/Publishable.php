<?php

namespace App;

use App\Observers\RecordAuthorObserver;
use App\Observers\RecordPublishDateObserver;

trait Publishable
{
    /**
     * Register the observers.
     */
    public static function bootPublishable(): void
    {
        static::observe(new RecordAuthorObserver);
        static::observe(new RecordPublishDateObserver);
    }
}
