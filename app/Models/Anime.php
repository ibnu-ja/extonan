<?php

namespace App\Models;

use App\Data\Anilist\AnilistMediaData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Oddvalue\LaravelDrafts\Concerns\HasDrafts;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @property-read AnilistMediaData|null $metadata
 */
class Anime extends BasePost
{
    use HasDrafts, HasTranslatableSlug, HasTranslations, Searchable;

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
    protected $appends = ['can'];

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

    public function replicate(?array $except = null): static
    {
        return parent::replicate(array_merge($except ?? [], ['season', 'season_year']));
    }

    public function posts(): MorphMany
    {
        return $this->morphMany(Post::class, 'postable');
    }

    /**
     * @param  string  ...$genres
     */
    public function scopeGenreIn(Builder $query, ...$genres): void
    {
        $query->whereJsonContains('metadata->genres', $genres);
    }

    /**
     * @param  string  ...$genres
     */
    public function scopeGenreNotIn(Builder $query, ...$genres): void
    {
        $query->whereJsonContains('metadata->genres', $genres, not: true);
    }

    public function scopeTagIn(Builder $query, int ...$tags): void
    {
        $query->whereJsonContains('metadata->tags', array_map(fn (int $tag): array => ['id' => $tag], $tags));
    }

    public function scopeTagNotIn(Builder $query, int ...$tags): void
    {
        foreach ($tags as $tag) {
            $query->whereJsonContains('metadata->tags', [['id' => $tag]], not: true);
        }
    }

    public function scopeTitle(Builder $query, string $title): void
    {
        $query->whereJsonContainsLocales('title', ['en', 'native', 'jp', 'id'], '%'.$title.'%', 'ilike');
    }

    public function scopeSeasonIn(Builder $query, ...$seasons): void
    {
        $query->whereIn(DB::raw('CONCAT(INITCAP(season), \' \', season_year)'), $seasons);
    }

    public function scopeSeasonNotIn(Builder $query, ...$seasons): void
    {
        $query->whereNotIn(DB::raw('CONCAT(INITCAP(season), \' \', season_year)'), $seasons);
    }

    /**
     * searching title with Scout with caveat
     *
     * @link https://github.com/spatie/laravel-query-builder/issues/147
     */
    public function scopeSearchTitle(Builder $query, string $search): void
    {
        $builder = self::search($search);
        $builder->limit = 10000000;
        $result = $builder->raw();
        $ids = array_column($result['hits'], 'id');

        $orders = array_map(fn ($id) => sprintf('id = %d desc', $id), $ids);
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
