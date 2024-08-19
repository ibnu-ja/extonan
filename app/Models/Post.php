<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Sluggable\SlugOptions;

class Post extends BasePost
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'description', 'anilist_id', 'metadata'];

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
