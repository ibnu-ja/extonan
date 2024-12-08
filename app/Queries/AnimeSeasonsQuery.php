<?php

namespace App\Queries;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/*
 * usage: (new AnimeSeasonsQuery())->builder()->get()->pluck('season_year')
 */
final readonly class AnimeSeasonsQuery
{
    /**
     * @return Builder
     */
    public function builder(): Builder
    {
        $subquery = DB::table('anime')
            ->select(
                DB::raw('metadata->>\'season\' as season'),
                DB::raw('metadata->>\'seasonYear\' as year'),
                DB::raw('CONCAT(metadata->>\'season\', \' \', metadata->>\'seasonYear\') AS season_year'))
            ->distinct();

        return DB::query()
            ->fromSub($subquery, 'anime_season_order')
            ->select('season_year')
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
