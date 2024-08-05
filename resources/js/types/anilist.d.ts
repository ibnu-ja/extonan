export type AnilistMedia = {
  Media: Anime
}

export type Anime = {
  id: number
  episodes: number
  description: string
  coverImage: {
    extraLarge: string
    large: string
    medium: string
    color: string
  }
  startDate: {
    year: number
    month: number
    day: number
  }
  endDate: {
    year: number
    month: number
    day: number
  }
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
