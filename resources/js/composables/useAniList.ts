import { AxiosResponse } from 'axios'
import { GraphQLResponse } from '@/types'
import { AnilistMedia } from '@/types/anilist'

export function useAnime() {
  const animeApi = async (id: number, useMalId = false) => {
    // if (apiSearchId.value == null || apiSearchId.value < 0) {
    //   return
    // }
    const query = `
    query ($id: Int, $idMal: Int) {
      Media (id: $id, idMal: $idMal , type: ANIME) {
        id
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
        characters {
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
        seasonInt
        genres
        title {
          romaji
          english
          native
        }
      }
    }`

    let variables
    if (useMalId) {
      variables = {
        idMal: id,
      }
    } else variables = { id: id }

    try {
      const response: AxiosResponse<GraphQLResponse<AnilistMedia>> = await axios.post('https://graphql.anilist.co/',
        { query, variables },
        {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
        })
      // Access response data
      if (response.data.errors) {
        // Handle GraphQL errors
        throw new Error('GraphQL errors: ' + JSON.stringify(response.data.errors))
      }
      return response.data.data.Media
      // const { romaji, english: en, native } = anime.title
      // anilistData.value = anime
      // form.title = {
      //   romaji,
      //   en,
      //   native,
      // }
      // show.value = !show.value
      // form.anilist_id = anime.id
    } catch (error) {
      // if (axios.isAxiosError(error))
      console.error(error)
    }
  }

  return { animeApi }
}
