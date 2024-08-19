import { TranslatableField } from '@/types/formHelper'
import { Post } from '@/types/index'

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
} & LatestAnimeItem & Post
