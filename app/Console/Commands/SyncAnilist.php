<?php

namespace App\Console\Commands;

use App\Models\Anime;
use App\Services\AnilistService;
use Illuminate\Console\Command;

class SyncAnilist extends Command
{
    protected $signature = 'app:sync-anilist';

    protected $description = 'Sync full AniList metadata for all anime';

    public function handle(AnilistService $anilist): void
    {
        $animeList = Anime::all();
        $this->info(sprintf('Syncing %d anime from AniList', $animeList->count()));

        foreach ($animeList as $anime) {
            $anilistId = $anime->anilist_id;

            if (! $anilistId) {
                continue;
            }

            $this->info(sprintf('Fetching AniList data for anime %d (anilist_id: %d)', $anime->id, $anilistId));

            $data = $anilist->fetchMedia($anilistId);

            if ($data === null) {
                $this->warn(sprintf('Failed to fetch data for anime %d', $anime->id));

                continue;
            }

            $anime->update(['metadata' => $data]);
        }
    }
}
