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

    public function thumbnail(): Attribute {
        return Attribute::make(
            function () {
                if ($this->hasMedia('thumbnail')) {
                    $this->loadMediaWithVariants('thumbnail');
                    $media = $this->firstMedia('thumbnail');
                    return [
                        'medium' => $media->findVariant('medium')->getUrl(),
                        'large' => $media->findVariant('large')->getUrl(),
                        'extraLarge' => $media->getUrl(),
                    ];
                }
                else return null;
            }
        );
    }
}
