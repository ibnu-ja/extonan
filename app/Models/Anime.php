<?php

namespace App\Models;

use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Anime extends Post
{
    use HasTranslations;

    public $table = 'anime';

    /**
     * @var string[]
     */
    public array $translatable = ['title', 'description'];

    /**
     * @var string[]
     */
    public $fillable = ['title', 'description', 'anilist_id'];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(60);
    }
}
