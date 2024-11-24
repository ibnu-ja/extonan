export type Album = {
  arrangers: Arranger[]
  barcode: string
  catalog: string
  categories: string[]
  category: string
  classification: string
  composers: Arranger[]
  covers: Cover[]
  discs: Disc[]
  distributor: Distributor
  link: string
  lyricists: Arranger[]
  media_format: string
  meta: Meta
  name: string
  names: DistributorNames
  notes: string
  organizations: Distributor[]
  performers: Arranger[]
  picture_full: string
  picture_small: string
  picture_thumb: string
  platforms: string[]
  products: Product[]
  publish_format: string
  publisher: Distributor
  related: Related[]
  release_date: Date
  release_events: unknown[]
  release_price: ReleasePrice
  reprints: unknown[]
  stores: Store[]
  vgmdb_link: string
  vocals: Arranger[]
  votes: number
}

export type Arranger = {
  link: string
  names: ArrangerNames
}

export type ArrangerNames = {
  en: string
  ja?: string
}

export type Cover = {
  full: string
  medium: string
  name: string
  thumb: string
}

export type Disc = {
  disc_length: string
  name: string
  tracks: Track[]
}

export type Track = {
  names?: { [key: string]: string | null }
  track_length: string
}

// export type TrackNames = {
//   Japanese: string | null
//   Romaji: string | null
// }

export type Distributor = {
  link: string
  names: DistributorNames
  role: string
}

export type DistributorNames = {
  'en': string
  'ja': string
  'ja-latn': string
}

export type Meta = {
  added_date: string
  edited_date: string
  fetched_date: string
  ttl: number
  visitors: number
}

export type Product = {
  link: string
  names: DistributorNames
}

export type Related = {
  catalog: string
  link: string
  names: DistributorNames
  type: Type
}

export type Type = 'anime' | 'bonus'

export type ReleasePrice = {
  currency: string
  price: number
}

export type Store = {
  link: string
  name: string
}
