import { TranslatableField } from '@/types/formHelper'
import { CoverImage } from '@/types/anilist'
import { Media, Post } from '@/types/index'
import { Resource } from '@/types/anime'
import { Album } from '@/types/vgmdb'

export type MV = {
  id: string
  description: TranslatableField
  slug: TranslatableField
  title: TranslatableField
  metadata: {
    post_type: string
    vgmdb_data?: Album | null
  }
  postable_type: string
  postable_id: number
  thumbnail: CoverImage | null
  links: Resource[]
  thumbnail_item: Media | null
} & Post

export type MVPostItem = {
  id?: string
  description: TranslatableField
  title: TranslatableField
  metadata: {
    post_type: string
    vgmdb_data?: Album | null
  }
  is_published: boolean
  thumbnail_item: Media | null
  links: Resource[]
}
