import { TranslatableField } from '@/types/formHelper'
import { CoverImage } from '@/types/anilist'
import { Post } from '@/types/index'

export type MV = {
  id: string
  description: TranslatableField
  slug: TranslatableField
  title: TranslatableField
  metadata: {
    post_type: string
    ep_no: string
  }
  postable_type: string
  postable_id: number
  thumbnail: CoverImage | null
} & Post
