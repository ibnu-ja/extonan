<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
    protected $fillable = ['title', 'description', 'anilist_id'];

    /**
     * @var string[]
     */
    protected $appends = ['link'];

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

    /**
     * Get the anime link
     */
    protected function link(): Attribute
    {
        return Attribute::make(
            get: fn () => route('anime.show', $this->attributes['id']),
        );
    }
}
