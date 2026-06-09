<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnilistService
{
    private const string ANILIST_API = 'https://graphql.anilist.co';

    private const int PER_PAGE = 50;

    private const string MEDIA_FIELDS = <<<'GRAPHQL'
    id
    idMal
    episodes
    coverImage {
      extraLarge
      large
      medium
      color
    }
    startDate {
      year
      month
      day
    }
    endDate {
      year
      month
      day
    }
    studios {
      edges {
        node {
          id
          name
        }
      }
    }
    description
    characters(sort: [ROLE, ID]) {
      edges {
        node {
          id
          name { full }
          image { large medium }
        }
        role
        voiceActors(language: JAPANESE) {
          id
          image { large medium }
          name { full }
        }
      }
    }
    seasonYear
    season
    genres
    title {
      romaji
      english
      native
    }
    tags {
      category
      description
      id
      isAdult
      isGeneralSpoiler
      isMediaSpoiler
      name
      rank
    }
    bannerImage
GRAPHQL;

    private const string ANIME_QUERY = <<<'GRAPHQL'
query ($id: Int, $idMal: Int) {
  Media(id: $id, idMal: $idMal, type: ANIME) {
GRAPHQL
        .self::MEDIA_FIELDS
        .<<<'GRAPHQL'

  }
}
GRAPHQL;

    private const string ANIME_BATCH_QUERY = <<<'GRAPHQL'
query ($idIn: [Int], $perPage: Int, $page: Int) {
  Page(perPage: $perPage, page: $page) {
    pageInfo {
      hasNextPage
    }
    media(type: ANIME, id_in: $idIn) {
GRAPHQL
        .self::MEDIA_FIELDS
        .<<<'GRAPHQL'

    }
  }
}
GRAPHQL;

    public function getAnimeQuery(): string
    {
        return self::ANIME_QUERY;
    }

    public function fetchMedia(int $id, bool $useMalId = false): ?array
    {
        $variables = $useMalId ? ['idMal' => $id] : ['id' => $id];

        $response = Http::post(self::ANILIST_API, [
            'query' => self::ANIME_QUERY,
            'variables' => $variables,
        ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json('data.Media');
    }

    /** @return array<int, array> */
    public function fetchMediaBatch(array $idIn): array
    {
        $allMedia = [];
        $page = 1;

        do {
            Log::info('AnilistService: fetching page '.$page, [
                'id_count' => count($idIn),
                'per_page' => self::PER_PAGE,
            ]);

            $response = Http::post(self::ANILIST_API, [
                'query' => self::ANIME_BATCH_QUERY,
                'variables' => [
                    'idIn' => $idIn,
                    'perPage' => self::PER_PAGE,
                    'page' => $page,
                ],
            ]);

            if ($response->failed()) {
                Log::error('AnilistService: request failed on page '.$page, [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                break;
            }

            $pageData = $response->json('data.Page');
            $media = $pageData['media'] ?? [];
            $allMedia = array_merge($allMedia, $media);
            $hasNextPage = $pageData['pageInfo']['hasNextPage'] ?? false;

            Log::info('AnilistService: page '.$page.' returned '.count($media).' items', [
                'has_next_page' => $hasNextPage,
                'total_so_far' => count($allMedia),
            ]);

            $page++;
        } while ($hasNextPage);

        return $allMedia;
    }

    public function fetchBanners(array $idIn): array
    {
        $query = <<<'GRAPHQL'
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
      bannerImage
    }
  }
}
GRAPHQL;

        $response = Http::post(self::ANILIST_API, [
            'query' => $query,
            'variables' => ['idIn' => $idIn],
        ]);

        if ($response->failed()) {
            return [];
        }

        return $response->json('data.Page.media') ?? [];
    }

    public function fetchMediaTags(): array
    {
        $query = <<<'GRAPHQL'
query {
  MediaTagCollection {
    id
    name
    isAdult
    category
    description
    isGeneralSpoiler
    isMediaSpoiler
    rank
  }
}
GRAPHQL;

        $response = Http::post(self::ANILIST_API, [
            'query' => $query,
        ]);

        if ($response->failed()) {
            return [];
        }

        return $response->json('data.MediaTagCollection') ?? [];
    }
}
