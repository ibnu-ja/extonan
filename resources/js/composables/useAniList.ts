import axios, { AxiosResponse } from 'axios'
import { GraphQLResponse } from '@/types'
import {
  AnimeMediaAutofillResponse,
  AnimeMediaHomepageResponse,
  MediaApiResponse,
  PaginatedMediaApiResponse,
} from '@/types/anilist'

import genres from '@/assets/data/genres.json'
import tags from '@/assets/data/tags.json'

export function useAnime() {
  const hitApi = async <T>(variables: unknown, query: string): Promise<T | undefined> => {
    try {
      const response: AxiosResponse<GraphQLResponse<T>> = await axios.post('https://graphql.anilist.co/',
        { query, variables },
        {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
        })
      if (response.data.errors) {
        // Handle GraphQL errors
        throw new Error('GraphQL errors: ' + JSON.stringify(response.data.errors))
      }
      return response.data.data
    } catch (error) {
      // if (axios.isAxiosError(error))
      console.error(error)
    }
  }

  const animeApi = async (id: number, useMalId = false) => {
    // if (apiSearchId.value == null || apiSearchId.value < 0) {
    //   return
    // }
    const query = `
    query ($id: Int, $idMal: Int) {
      Media (id: $id, idMal: $idMal , type: ANIME) {
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
        characters (sort: [ROLE, ID]) {
          edges { # Array of character edges
            node { # Character node
              id
              name {
                full
              }
              image {
                large
                medium
              }
            }
            role
            voiceActors(language: JAPANESE) { # Array of voice actors of this character for the anime
              id
              image {
                large
                medium
              }
              name {
                full
              }
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
      }
    }`

    let variables
    if (useMalId) {
      variables = {
        idMal: id,
      }
    } else variables = { id: id }

    const response = await hitApi<MediaApiResponse<AnimeMediaAutofillResponse>>(variables, query)
    return response?.Media
  }

  const homeAnimeApi = async (id: number[]) => {
    const query = `
    query ($id_in: [Int]) {
      Page {
        media(id_in: $id_in) {
          id
          coverImage {
            extraLarge
            large
            medium
            color
          }
        }
      }
    }
    `

    const variables = {
      id_in: id,
    }

    const response = await hitApi<PaginatedMediaApiResponse<AnimeMediaHomepageResponse>>(variables, query)
    return response?.Page.media
  }

  return { hitApi, animeApi, homeAnimeApi, genres, tags }
}
