<?php

namespace App\Queries;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/*
 * usage: (new AnimeSeasonsQuery())->builder()->get()->pluck('season_year')
 */
final readonly class AnimeSeasonsQuery
{
    public function builder(): Builder
    {
        $subquery = DB::table('anime')
            ->select(
                'season',
                DB::raw('season_year AS year'),
                DB::raw('CONCAT(INITCAP(season), \' \', season_year) AS season_year_label'))
            ->distinct()
            ->whereNotNull('season')
            ->whereNotNull('season_year');

        return DB::query()
            ->fromSub($subquery, 'anime_season_order')
            ->select(DB::raw('season_year_label AS season_year'))
            ->orderByRaw('CAST(year AS INTEGER)')
            ->orderByRaw('CASE
                WHEN season = \'WINTER\' THEN 1
                WHEN season = \'SPRING\' THEN 2
                WHEN season = \'SUMMER\' THEN 3
                WHEN season = \'FALL\' THEN 4
                ELSE 5 END
            ');
    }
}
