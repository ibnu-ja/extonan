<?php

namespace App\Models;

use App\Http\Requests\ShinraiPostType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Plank\Mediable\Mediable;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Post extends BasePost
{
    use HasTranslations, HasTranslatableSlug, Mediable;

    /**
     * @var string[]
     */
    public array $translatable = ['title', 'description', 'slug'];

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'description', 'metadata', 'is_published'];

    protected $casts = ['metadata' => 'object'];

    protected $appends = ['thumbnail', 'can'];

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
//        natural sorting ->orderBy(DB::raw('LENGTH(name), name'))
        return $this->resources()->where('type', '=', 'link');
    }

    public function embeds(): HasMany
    {
        return $this->resources()->where('type', '=', 'embed');
    }

    public function thumbnail(): Attribute
    {
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
                } else return null;
            }
        );
    }

    public function thumbnailItem(): Attribute
    {
        return Attribute::make(
            function () {
                return $this->firstMedia('thumbnail');
            }
        );
    }

    public function scopePrev(Builder $query, BasePost $basePost, Post $post): void
    {
        $query->whereHasMorph('postable', [Anime::class], function (Builder $query) use ($basePost) {
            $query->select('id')->where('id', '=', $basePost->id)->with('author');
        })->with(['author'])->where('title', '<', $post->title)->orderBy('title');
    }

    public function scopeNext(Builder $query, BasePost $basePost, Post $post): void
    {
        $query->whereHasMorph('postable', [Anime::class], function (Builder $query) use ($basePost) {
            $query->select('id')->where('id', '=', $basePost->id)->with('author');
        })->with(['author'])->where('title', '>', $post->title)->orderBy('title');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeShinrai(Builder $query): void
    {
        $query->whereIn('metadata->post_type', array_column(ShinraiPostType::cases(), 'value'));
    }

    public function scopeOrderByEpisodeAndNativeTitle(Builder $query, $direction = 'asc'): void
    {
        $direction = strtolower($direction) === 'desc' ? 'DESC' : 'ASC'; // sanitize direction

        // Use jsonb_exists to avoid the '?' operator (PDO/Laravel will not mangle this).
        $orderExpr = "CASE WHEN pg_catalog.jsonb_exists(metadata, 'ep_no') " .
            "THEN (metadata->>'ep_no')::numeric END {$direction} NULLS LAST";

        $query->orderByRaw($orderExpr)
            ->orderByDesc('title->native');
    }
}
