import { TranslatableField } from '@/types/formHelper'
import { Post } from '@/types/index'
import { AnimeMediaAutofillResponse } from '@/types/anilist'

export type LatestAnimeItem = {
  id: number
  anilist_id: number
  slug: string
  description: TranslatableField
  title: TranslatableField
  link: string
} & Post

export type AnimeData = {
  description: TranslatableField
  metadata: AnimeMediaAutofillResponse
} & LatestAnimeItem & Post

export type EpisodeData = Post & {
  title: TranslatableField
  description: TranslatableField
  id: number
}

export type ResourceItem = {
  name: string
  value: string
}

export type Resource = {
  name: string
  value: ResourceItem[]
  type: 'link' | 'embed'
}
