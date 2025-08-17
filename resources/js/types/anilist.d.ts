import { TranslatableField } from '@/types/formHelper'

export type MediaApiResponse<T> = {
  Media: T
}

export type PaginatedMediaApiResponse<T> = {
  Page: {
    media: T[]
  }
}

export type CoverImage = {
  extraLarge: string
  large: string
  medium: string
  color: string
}

export type FuzzyDate = {
  year: number | null
  month: number | null
  day: number | null
}

export type AnimeMetadataTag = {
  id: number
  name: string
  rank: number
  isAdult: boolean
  category: string
  isMediaSpoiler: boolean
  isGeneralSpoiler: boolean
}

export type Language = 'romaji' | 'english' | 'native'

export type AnimeMediaAutofillResponse = {
  id: number
  tags: AnimeMetadataTag[]
  idMal: number
  title: TranslatableField<Language>
  genres: string[]
  season: 'WINTER' | 'SPRING' | 'SUMMER' | 'FALL'
  endDate: FuzzyDate
  studios: {
    edges: {
      node: {
        id: number
        name: string
      }
    }[]
  }
  episodes: number
  seasonInt: number
  startDate: FuzzyDate
  characters: {
    edges: {
      node: {
        id: number
        name: {
          full: string
        }
        image: {
          large: string
          medium: string
        }
      }
      role: 'MAIN' | 'SUPPORTING' | 'BACKGROUND'
      voiceActors: {
        id: number
        name: {
          full: string
        }
        image: {
          large: string
          medium: string
        }
      }[]
    }[]
  }
  coverImage: CoverImage
  seasonYear: number
  // WINTER
  // Months December to February

  // SPRING
  // Months March to May

  // SUMMER
  // Months June to August

  // FALL
  // Months September to November
  bannerImage: string | null | undefined
  description: string
}

export type AnimeMediaHomepageResponse = {
  id: number
  coverImage: CoverImage
}
