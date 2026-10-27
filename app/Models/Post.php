<?php

namespace App\Models;

use App\Enums\ResourceType;
use App\Http\Requests\ShinraiPostType;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Plank\Mediable\Media;
use Plank\Mediable\Mediable;
use Plank\Mediable\MediableCollection;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property array<array-key, mixed> $description
 * @property array<array-key, mixed>|null $slug
 * @property array<array-key, mixed> $title
 * @property int $author_id
 * @property object|null $metadata
 * @property string|null $postable_type
 * @property int|null $postable_id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property string|null $uuid
 * @property CarbonImmutable|null $published_at
 * @property bool $is_published
 * @property bool $is_current
 * @property string|null $publisher_type
 * @property int|null $publisher_id
 * @property-read User|null $author
 * @property-read mixed $can
 * @property-read MediableCollection<int, Post> $drafts
 * @property-read int|null $drafts_count
 * @property-read \App\Models\Resource|null $embed
 * @property-read static|null $draft
 * @property-read array $translatable_columns_from
 * @property-read Collection<int, \App\Models\Resource> $links
 * @property-read int|null $links_count
 * @property-read Collection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $postable
 * @property-read Model|null $publisher
 * @property-read Collection<int, \App\Models\Resource> $resources
 * @property-read int|null $resources_count
 * @property-read MediableCollection<int, Post> $revisions
 * @property-read int|null $revisions_count
 * @property-read \App\Models\Resource|null $saluran
 * @property-read mixed $thumbnail
 * @property-read mixed $thumbnail_item
 * @property-read mixed $translations
 *
 * @method static \Plank\Mediable\MediableCollection<int, static> all($columns = ['*'])
 * @method static Builder<static>|Post current()
 * @method static Builder<static>|Post excludeRevision(\Illuminate\Database\Eloquent\Model|int $exclude)
 * @method static \Database\Factories\PostFactory factory($count = null, $state = [])
 * @method static \Plank\Mediable\MediableCollection<int, static> get($columns = ['*'])
 * @method static Builder<static>|Post newModelQuery()
 * @method static Builder<static>|Post newQuery()
 * @method static Builder<static>|Post next(\App\Models\BasePost $basePost, \App\Models\Post $post)
 * @method static Builder<static>|Post orderByEpisodeAndNativeTitle()
 * @method static Builder<static>|Post prev(\App\Models\BasePost $basePost, \App\Models\Post $post)
 * @method static Builder<static>|Post query()
 * @method static Builder<static>|Post shinrai()
 * @method static Builder<static>|Post visible()
 * @method static Builder<static>|Post whereAuthorId($value)
 * @method static Builder<static>|Post whereCreatedAt($value)
 * @method static Builder<static>|Post whereDescription($value)
 * @method static Builder<static>|Post whereHasMedia($tags = [], bool $matchAll = false)
 * @method static Builder<static>|Post whereHasMediaMatchAll($tags)
 * @method static Builder<static>|Post whereId($value)
 * @method static Builder<static>|Post whereIsCurrent($value)
 * @method static Builder<static>|Post whereIsPublished($value)
 * @method static Builder<static>|Post whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|Post whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|Post whereLocale(string $column, string $locale)
 * @method static Builder<static>|Post whereLocales(string $column, array $locales)
 * @method static Builder<static>|Post whereMetadata($value)
 * @method static Builder<static>|Post wherePostableId($value)
 * @method static Builder<static>|Post wherePostableType($value)
 * @method static Builder<static>|Post wherePublishedAt($value)
 * @method static Builder<static>|Post wherePublisherId($value)
 * @method static Builder<static>|Post wherePublisherType($value)
 * @method static Builder<static>|Post whereSlug($value)
 * @method static Builder<static>|Post whereTitle($value)
 * @method static Builder<static>|Post whereUpdatedAt($value)
 * @method static Builder<static>|Post whereUuid($value)
 * @method static Builder<static>|Post withMedia($tags = [], bool $matchAll = false, bool $withVariants = false)
 * @method static Builder<static>|Post withMediaAndVariants($tags = [], bool $matchAll = false)
 * @method static Builder<static>|Post withMediaAndVariantsMatchAll($tags = [])
 * @method static Builder<static>|Post withMediaMatchAll(bool $tags = [], bool $withVariants = false)
 * @method static Builder<static>|Post withoutCurrent()
 * @method static Builder<static>|Post withoutSelf()
 *
 * @mixin \Eloquent
 */
#[Fillable(['title', 'description', 'metadata', 'is_published'])]
class Post extends BasePost
{
    use HasTranslatableSlug, HasTranslations, Mediable;

    /**
     * @var string[]
     */
    public array $translatable = ['title', 'description', 'slug'];

    protected $appends = ['thumbnail', 'can'];

    /**
     * TODO check, optimiasi kolom metadata. VGMDB check
     *
     * @return string[]
     */
    protected function casts(): array
    {
        return ['metadata' => 'object'];
    }

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

    /**
     * Single resources table.
     * links = 1 file per relasi, mirror per file
     * embed = 1 relasi, simpan mirror dalam value sbg array. quality handle di embed
     * saluran = sda
     */
    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function links(): HasMany
    {
        //        natural sorting ->orderBy(DB::raw('LENGTH(name), name'))
        return $this->resources()->where('type', ResourceType::Link);
    }

    public function embed(): HasOne
    {
        return $this->hasOne(Resource::class)->where('type', ResourceType::Embed);
    }

    public function saluran(): HasOne
    {
        return $this->hasOne(Resource::class)->where('type', ResourceType::Saluran);
    }

    public function thumbnail(): Attribute
    {
        return Attribute::make(
            function () {
                if (! $this->hasMedia('thumbnail')) {
                    return null;
                }

                if (! $this->relationLoaded('media')) {
                    $this->loadMediaWithVariants('thumbnail');
                }

                $media = $this->firstMedia('thumbnail');

                return [
                    'medium' => $media->findVariant('medium')->getUrl(),
                    'large' => $media->findVariant('large')->getUrl(),
                    'extraLarge' => $media->getUrl(),
                ];
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
     * TODO gunakan syntax scope baru
     */
    public function scopeShinrai(Builder $query): void
    {
        $query->whereIn('metadata->post_type', array_column(ShinraiPostType::cases(), 'value'));
    }

    /**
     * TODO gunakan syntax scope baru
     */
    public function scopeOrderByEpisodeAndNativeTitle(Builder $query): void
    {
        $query
            ->orderByRaw('"metadata" ->> \'ep_no\' collate "numeric"')
            ->orderByDesc('title->native');
    }
}
