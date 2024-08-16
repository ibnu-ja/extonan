import { TranslatableField } from '@/types/formHelper'
import { Post } from '@/types/index'

export type AnimeData = {
  id: number
  anilist_id: number
  description: TranslatableField
  slug: string
  title: TranslatableField
  link: string
} & Post
