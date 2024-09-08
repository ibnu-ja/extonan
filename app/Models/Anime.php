<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Anime extends BasePost
{
    use HasTranslations, HasTranslatableSlug;

    public $table = 'anime';

    /**
     * @var string[]
     */
    public array $translatable = ['title', 'description', 'slug'];

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'description', 'anilist_id', 'metadata'];

    /**
     * @var string[]
     */
    protected $appends = ['link', 'can'];

    protected $casts = ['metadata' => 'object'];

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
            get: fn() => route('anime.show', $this->attributes['id']),
        );
    }

    public function posts(): MorphMany
    {
        return $this->morphMany(Post::class, 'postable');
    }
}
