<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Oddvalue\LaravelDrafts\Concerns\HasDrafts;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Anime extends BasePost
{
    use HasTranslations, HasTranslatableSlug, HasDrafts, Searchable;

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

    /**
     * @param Builder $query
     * @param string ...$tags
     * @return void
     */
    public function scopeTagIn(Builder $query, ...$tags): void
    {
        $query->whereExists(function ($query) use ($tags) {
            $query->select(DB::raw(1))
                ->fromRaw('jsonb_array_elements(metadata->\'tags\') AS tag')
                ->whereIn('tag->name', $tags);
        });
    }

    /**
     * @param Builder $query
     * @param string ...$tags
     * @return void
     */
    public function scopeTagNotIn(Builder $query, ...$tags): void
    {
        $query->whereExists(function ($query) use ($tags) {
            $query->select(DB::raw(1))
                ->fromRaw('jsonb_array_elements(metadata->\'tags\') AS tag')
                ->whereNotIn('tag->name', $tags);
        });
    }

    public function scopeTitle(Builder $query, string $title): void
    {
        $query->whereJsonContainsLocales('title', ['en', 'native', 'jp', 'id'], '%' . $title . '%', 'ilike');
    }

    public function scopeSeasonIn(Builder $query, ...$seasons): void
    {
        $query->whereIn(DB::raw('CONCAT(metadata->>\'season\', \' \', metadata->>\'seasonYear\')'), $seasons);
    }

    public function scopeSeasonNotIn(Builder $query, ...$seasons): void
    {
        $query->whereNotIn(DB::raw('CONCAT(metadata->>\'season\', \' \', metadata->>\'seasonYear\')'), $seasons);
    }


    /**
     * searching title with Scout with caveat
     * @link https://github.com/spatie/laravel-query-builder/issues/147
     */
    public function scopeSearchTitle(Builder $query, string $search): void
    {
        $builder = self::search($search);
        $builder->limit = 10000000;
        $result = $builder->raw();
        $ids = array_column($result['hits'], 'id');

        $orders = array_map(fn($id) => sprintf("id = %d desc", $id), $ids);
        $rawOrder = implode(', ', $orders);
        $query->whereIn('id', $ids);

        if (count($ids)) {
            $query->orderByRaw($rawOrder);
        }
    }

    public function toSearchableArray(): array
    {
        return [
            'title' => $this->getTranslations('title'),
        ];
    }
}
