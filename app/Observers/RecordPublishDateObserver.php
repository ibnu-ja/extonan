<?php

namespace App\Observers;

use App\Models\BasePost;

class RecordPublishDateObserver
{
    public function updating(BasePost $model): void
    {
        if (request()->has('is_published') && request()->get('is_published') === true && $model->getOriginal('is_published') === false) {
            $model->published_at = now();
        }
    }
}
