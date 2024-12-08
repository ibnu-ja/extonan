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

export type AnimeMediaAutofillResponse = {
  id: number
  idMal: number
  episodes: number
  description: string
  coverImage: CoverImage
  startDate: FuzzyDate
  endDate: FuzzyDate
  studios: {
    edges: {
      node: {
        id: number
        name: string
      }
    }[]
  }
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
  tags: {
    category: string
    id: number
    isAdult: boolean
    isGeneralSpoiler: boolean
    isMediaSpoiler: boolean
    name: string
    rank: number
  }
  seasonYear: number
  // WINTER
  // Months December to February

  // SPRING
  // Months March to May

  // SUMMER
  // Months June to August

  // FALL
  // Months September to November
  season: 'WINTER' | 'SPRING' | 'SUMMER' | 'FALL'
  seasonInt: number
  genres: string[]
  title: {
    romaji: string
    english: string
    native: string
  }
}

export type AnimeMediaHomepageResponse = {
  id: number
  coverImage: CoverImage
}
