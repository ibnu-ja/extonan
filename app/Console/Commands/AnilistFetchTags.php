<?php

namespace App\Console\Commands;

use App\Services\AnilistService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('anilist:fetch-tags')]
#[Description('Fetch MediaTagCollection from AniList and cache it locally')]
class AnilistFetchTags extends Command
{
    public function handle(AnilistService $anilist): int
    {
        $this->info('Fetching MediaTagCollection from AniList...');

        $tags = $anilist->fetchMediaTags();

        if (empty($tags)) {
            $this->error('Failed to fetch tags from AniList API.');

            return self::FAILURE;
        }

        $path = storage_path('app/anilist-tags.json');
        file_put_contents($path, json_encode($tags, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info(sprintf('Saved %d tags to %s', count($tags), $path));

        return self::SUCCESS;
    }
}
