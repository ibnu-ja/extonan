<?php

namespace App\Console\Commands;

use App\Models\Anime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncAnilist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-anilist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $animeToBeUpdated = Anime::all();
        $anilistQuery = '
query ($idIn: [Int], $perPage: Int, $page: Int) {
  Page(perPage: $perPage, page: $page) {
    pageInfo {
      currentPage
      hasNextPage
      lastPage
      perPage
      total
    }
    media(type: ANIME, id_in: $idIn) {
      id
      tags {
        category
        id
        isAdult
        isGeneralSpoiler
        isMediaSpoiler
        name
        rank
      }
    }
  }
}
';

        $this->info(sprintf('Updating %d anime', $animeToBeUpdated->count()));
        $idIn = $animeToBeUpdated->unique('anilist_id')->pluck("anilist_id");
        $this->info('query anilist id: '.$idIn);
        $variables = [
            "idIn" => $idIn,
        ];

        $response = Http::post('https://graphql.anilist.co', [
            'query' => $anilistQuery,
            'variables' => $variables,
        ])->json()["data"]["Page"]["media"];

        $media = collect($response);

        //TODO pagination handle
        foreach ($animeToBeUpdated as $anime) {
            $anilist_id = ($anime->metadata->id);
            $metadata = collect($anime->metadata);
            $metadata['tags'] = $media->firstWhere('id', $anilist_id)['tags'];
            $anime->update([
                'metadata' => $metadata
            ]);
        }
    }
}
