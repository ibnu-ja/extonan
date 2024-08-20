<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Post extends BasePost
{
    use HasTranslations;

    /**
     * @var string[]
     */
    public array $translatable = ['title', 'description'];

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'description', 'metadata'];

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

    public function postable(): MorphTo
    {
        return $this->morphTo();
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }
}
