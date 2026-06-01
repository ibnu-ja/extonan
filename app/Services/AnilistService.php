<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AnilistService
{
    private const string ANILIST_API = 'https://graphql.anilist.co';

    private const string ANIME_QUERY = <<<'GRAPHQL'
query ($id: Int, $idMal: Int) {
  Media(id: $id, idMal: $idMal, type: ANIME) {
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
      id
      isAdult
      isGeneralSpoiler
      isMediaSpoiler
      name
      rank
    }
    bannerImage
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
}
