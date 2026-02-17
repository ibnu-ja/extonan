import { TranslatableField } from '@/types/formHelper'
import { BasePost } from '@/types/index'
import { AnimeMediaAutofillResponse, CoverImage } from '@/types/anilist'

export type BaseLangage = 'en' | 'id' | 'native' | 'romaji'

export type AnimeData = {
  id: number
  anilist_id: number
  slug: TranslatableField<BaseLangage>
  title: TranslatableField<BaseLangage>
  description: TranslatableField<BaseLangage>
  link: string
  metadata: AnimeMediaAutofillResponse
} & BasePost

export type EpisodeData = BasePost & {
  id: number
  description: TranslatableField<BaseLangage>
  slug: TranslatableField<BaseLangage>
  title: TranslatableField<BaseLangage>
  metadata: {
    ep_no?: string | null
    post_type?: 'bd' | 'tv' | 'movie' | null
  }
  postable_type: string
  postable_id: number
  thumbnail: CoverImage | null
  // TODO check type
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  media: any[]
}

export type ResourceItem = {
  name: string
  value: string
}

export type Resource = {
  id?: number
  name: string
  value: ResourceItem[]
  type: 'link' | 'embed'
}

export type Filter = {
  title?: string
  season_in?: string
  season_not_in?: string
  tag_in?: string
  tag_not_in?: string
  genre_in?: string
  genre_not_in?: string
}
