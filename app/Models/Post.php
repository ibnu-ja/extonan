<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Plank\Mediable\Mediable;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Post extends BasePost
{
    use HasTranslations,
        Mediable;

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
            ->generateSlugsFrom(['postable.title', 'title'])
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

    public function links(): HasMany
    {
        return $this->resources()->where('type', '=','link')->orderBy('name');
    }

    public function embeds(): HasMany
    {
        return $this->resources()->where('type', '=','embed')->orderBy('name');
    }

    public function thumbnail() {
        return Attribute::make(
            get: fn () => [
                'medium' => $this->getMedia('thumbnail')->findVariant('medium')->getUrl(),
                'large' => $this->getMedia('thumbnail')->findVariant('large')->getUrl(),
                'extraLarge' => $this->getMedia('thumbnail')->first()->getUrl(),
            ]
        );
    }
}
