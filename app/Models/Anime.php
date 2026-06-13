<?php

namespace App\Models;

use App\Data\Anilist\AnilistMediaData;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Oddvalue\LaravelDrafts\Concerns\HasDrafts;
use Plank\Mediable\Media;
use Plank\Mediable\MediableCollection;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property int $anilist_id
 * @property array<array-key, mixed> $description
 * @property array<array-key, mixed> $slug
 * @property array<array-key, mixed> $title
 * @property int $author_id
 * @property object $metadata
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property string|null $uuid
 * @property CarbonImmutable|null $published_at
 * @property bool $is_published
 * @property bool $is_current
 * @property string|null $publisher_type
 * @property int|null $publisher_id
 * @property string|null $season
 * @property int|null $season_year
 * @property-read User $author
 * @property-read mixed $can
 * @property-read MediableCollection<int, Anime> $drafts
 * @property-read int|null $drafts_count
 * @property-read static|null $draft
 * @property-read array $translatable_columns_from
 * @property-read Collection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read MediableCollection<int, Post> $posts
 * @property-read int|null $posts_count
 * @property-read Model|null $publisher
 * @property-read MediableCollection<int, Anime> $revisions
 * @property-read int|null $revisions_count
 * @property-read mixed $translations
 *
 * @method static \Plank\Mediable\MediableCollection<int, static> all($columns = ['*'])
 * @method static Builder<static>|Anime current()
 * @method static Builder<static>|Anime excludeRevision(\Illuminate\Database\Eloquent\Model|int $exclude)
 * @method static \Database\Factories\AnimeFactory factory($count = null, $state = [])
 * @method static Builder<static>|Anime genreIn(string ...$genres)
 * @method static Builder<static>|Anime genreNotIn(string ...$genres)
 * @method static \Plank\Mediable\MediableCollection<int, static> get($columns = ['*'])
 * @method static Builder<static>|Anime newModelQuery()
 * @method static Builder<static>|Anime newQuery()
 * @method static Builder<static>|Anime query()
 * @method static Builder<static>|Anime searchTitle(string $search)
 * @method static Builder<static>|Anime seasonIn(string ...$seasons)
 * @method static Builder<static>|Anime seasonNotIn(string ...$seasons)
 * @method static Builder<static>|Anime tagIn(int ...$tags)
 * @method static Builder<static>|Anime tagNotIn(int ...$tags)
 * @method static Builder<static>|Anime title(string $title)
 * @method static Builder<static>|Anime visible()
 * @method static Builder<static>|Anime whereAnilistId($value)
 * @method static Builder<static>|Anime whereAuthorId($value)
 * @method static Builder<static>|Anime whereCreatedAt($value)
 * @method static Builder<static>|Anime whereDescription($value)
 * @method static Builder<static>|Anime whereHasMedia($tags = [], bool $matchAll = false)
 * @method static Builder<static>|Anime whereHasMediaMatchAll($tags)
 * @method static Builder<static>|Anime whereId($value)
 * @method static Builder<static>|Anime whereIsCurrent($value)
 * @method static Builder<static>|Anime whereIsPublished($value)
 * @method static Builder<static>|Anime whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|Anime whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|Anime whereLocale(string $column, string $locale)
 * @method static Builder<static>|Anime whereLocales(string $column, array $locales)
 * @method static Builder<static>|Anime whereMetadata($value)
 * @method static Builder<static>|Anime wherePublishedAt($value)
 * @method static Builder<static>|Anime wherePublisherId($value)
 * @method static Builder<static>|Anime wherePublisherType($value)
 * @method static Builder<static>|Anime whereSeason($value)
 * @method static Builder<static>|Anime whereSeasonYear($value)
 * @method static Builder<static>|Anime whereSlug($value)
 * @method static Builder<static>|Anime whereTitle($value)
 * @method static Builder<static>|Anime whereUpdatedAt($value)
 * @method static Builder<static>|Anime whereUuid($value)
 * @method static Builder<static>|Anime withMedia($tags = [], bool $matchAll = false, bool $withVariants = false)
 * @method static Builder<static>|Anime withMediaAndVariants($tags = [], bool $matchAll = false)
 * @method static Builder<static>|Anime withMediaAndVariantsMatchAll($tags = [])
 * @method static Builder<static>|Anime withMediaMatchAll(bool $tags = [], bool $withVariants = false)
 * @method static Builder<static>|Anime withoutCurrent()
 * @method static Builder<static>|Anime withoutSelf()
 *
 * @mixin \Eloquent
 */
#[Table('anime')]
#[Fillable(['title', 'description', 'anilist_id', 'metadata', 'is_published'])]
class Anime extends BasePost
{
    use HasDrafts, HasTranslatableSlug, HasTranslations, Searchable;

    /**
     * @var string[]
     */
    public array $translatable = ['title', 'description', 'slug'];

    /**
     * @var string[]
     */
    protected $appends = ['can'];

    protected function casts(): array
    {
        return ['metadata' => AnilistMediaData::class];
    }

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

    #[Scope]
    protected function genreIn(Builder $query, string ...$genres): void
    {
        $query->whereJsonContains('metadata->genres', $genres);
    }

    #[Scope]
    protected function genreNotIn(Builder $query, string ...$genres): void
    {
        $query->whereJsonContains('metadata->genres', $genres, not: true);
    }

    #[Scope]
    protected function tagIn(Builder $query, int ...$tags): void
    {
        $query->whereJsonContains('metadata->tags', array_map(fn (int $tag): array => ['id' => $tag], $tags));
    }

    #[Scope]
    protected function tagNotIn(Builder $query, int ...$tags): void
    {
        foreach ($tags as $tag) {
            $query->whereJsonContains('metadata->tags', [['id' => $tag]], not: true);
        }
    }

    #[Scope]
    protected function title(Builder $query, string $title): void
    {
        $query->whereJsonContainsLocales('title', ['en', 'native', 'jp', 'id'], '%'.$title.'%', 'ilike');
    }

    #[Scope]
    protected function seasonIn(Builder $query, string ...$seasons): void
    {
        $query->whereIn(DB::raw('CONCAT(INITCAP(season), \' \', season_year)'), $seasons);
    }

    #[Scope]
    protected function seasonNotIn(Builder $query, string ...$seasons): void
    {
        $query->whereNotIn(DB::raw('CONCAT(INITCAP(season), \' \', season_year)'), $seasons);
    }

    /**
     * searching title with Scout with caveat
     *
     * @link https://github.com/spatie/laravel-query-builder/issues/147
     */
    #[Scope]
    protected function searchTitle(Builder $query, string $search): void
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
