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
        $debug = config('app.debug');
        $animeList = Anime::all();
        $total = $animeList->count();
        $this->info(sprintf('Found %d anime in database', $total));

        $idIn = $animeList->pluck('anilist_id')->filter()->values()->toArray();
        $this->info(sprintf('Querying %d anilist IDs from AniList API...', count($idIn)));

        $media = collect($anilist->fetchMediaBatch($idIn));
        $this->info(sprintf('Received %d media records from AniList', $media->count()));

        if ($debug) {
            $this->line(sprintf('Queried IDs: [%s]', implode(', ', $idIn)));
            $this->line(sprintf('Returned IDs: [%s]', implode(', ', $media->pluck('id')->toArray())));
            $this->newLine();
        }

        $updated = 0;
        $skipped = 0;
        $notFound = 0;
        $debugLog = [];

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($animeList as $anime) {
            $anilistId = $anime->anilist_id;
            $titles = $anime->getTranslations('title');
            $romaji = $titles['romaji'] ?? '';

            if (! $anilistId) {
                $skipped++;
                $bar->advance();

                if ($debug) {
                    $debugLog[] = sprintf('  [SKIP] #%d "%s" — no anilist_id', $anime->id, $romaji);
                }

                continue;
            }

            $found = $media->firstWhere('id', $anilistId);

            if (! $found) {
                $notFound++;
                $bar->advance();

                if ($debug) {
                    $debugLog[] = sprintf('  [MISS] #%d "%s" — anilist_id %d not in API response', $anime->id, $romaji, $anilistId);
                }

                continue;
            }

            $anime->update(['metadata' => $found]);
            $updated++;
            $bar->advance();

            if ($debug) {
                $debugLog[] = sprintf('  [OK]   #%d "%s" — anilist_id %d updated', $anime->id, $romaji, $anilistId);
            }
        }

        $bar->finish();
        $this->newLine(2);

        if ($debug && $debugLog !== []) {
            $this->line('<comment>Debug log:</comment>');
            foreach ($debugLog as $line) {
                $this->line($line);
            }
            $this->newLine();
        }

        $this->info(sprintf(
            'Done. Updated: %d | Skipped (no anilist_id): %d | Not found on AniList: %d',
            $updated,
            $skipped,
            $notFound,
        ));
    }
}
