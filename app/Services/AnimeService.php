<?php

namespace App\Services;

use App\Data\LabelValue;
use App\Data\TagItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\DataCollection;

class AnimeService
{
    public function getGenres(): DataCollection
    {
        return new DataCollection(LabelValue::class, collect(config('anime.genres', []))->map(fn (string $genre) => new LabelValue(
            key: $genre,
            value: __('anime.genres.'.$genre),
        )));
    }

    public function getTags(): DataCollection
    {
        $tags = Cache::remember('anilist-tags', now()->addCentury(), function () {
            $path = 'anilist-tags.json';

            return Storage::disk('private')->exists($path)
                ? json_decode(Storage::disk('private')->get($path), true)
                : [];
        });

        return new DataCollection(TagItem::class, array_map(fn (array $t) => new TagItem(
            id: $t['id'],
            name: $t['name'],
            isAdult: $t['isAdult'] ?? false,
        ), $tags));
    }

    public function getSortOptions(): DataCollection
    {
        return new DataCollection(LabelValue::class, collect(['title->romaji', '-title->romaji', 'created_at', '-created_at', 'updated_at', '-updated_at'])->map(function (string $sort) {
            $dir = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $field = ltrim($sort, '-');

            return new LabelValue(
                key: $sort,
                value: __('anime.sort.'.$field).' '.__('anime.sort_dir.'.$dir),
            );
        }));
    }
}
