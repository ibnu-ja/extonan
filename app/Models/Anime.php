<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Oddvalue\LaravelDrafts\Concerns\HasDrafts;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Anime extends BasePost
{
    use HasTranslations, HasTranslatableSlug, HasDrafts;

    public $table = 'anime';

    /**
     * @var string[]
     */
    public array $translatable = ['title', 'description', 'slug'];

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'description', 'anilist_id', 'metadata', 'is_published'];

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

    /**
     * @param Builder $query
     * @param string ...$genres
     * @return void
     */
    public function scopeGenreIn(Builder $query, ...$genres): void
    {
        $query->whereJsonContains('metadata->genres', $genres);
    }

    /**
     * @param Builder $query
     * @param string ...$genres
     * @return void
     */
    public function scopeGenreNotIn(Builder $query, ...$genres): void
    {
        $query->whereJsonContains('metadata->genres', $genres, not: true);
    }

    public function scopeTitle(Builder $query, string $title): void
    {
        $query->whereJsonContainsLocales('title', ['en', 'native', 'jp', 'id'], '%' . $title . '%', 'like');
    }

    public function scopeSeasonIn(Builder $query, ...$seasons): void
    {
        $query->where(DB::raw('CONCAT(metadata->>\'season\', \' \', metadata->>\'seasonYear\')'), $seasons);
    }

    public function scopeSeasonNotIn(Builder $query, ...$seasons): void
    {
        $query->whereNot(DB::raw('CONCAT(metadata->>\'season\', \' \', metadata->>\'seasonYear\')'), $seasons);
    }
}
