import { Media } from '@/types'

const appUrl = import.meta.env.VITE_APP_URL

export function srcSet(media: Media) {
  if (!media) return

  // const width300 = `${imageLink(media)!.small} 300w`
  const width600 = `${imageLink(media)!.medium} 600w`
  const width900 = `${imageLink(media)!.large} 900w`

  return `${width600}, ${width900}`
}

export function imageLink(media: Media | undefined | null) {
  // check if media is not undefined or null
  if (!media) return

  // const thumbnail = `${appUrl}/storage/${media.directory}/${media.filename}-thumb.${media.extension}`
  // const small = `${appUrl}/storage/${media.directory}/${media.filename}-small.${media.extension}`
  const medium = `${appUrl}/storage/${media.directory}/${media.filename}-medium.${media.extension}`
  const large = `${appUrl}/storage/${media.directory}/${media.filename}-large.${media.extension}`
  const original = `${appUrl}/storage/${media.directory}/${media.filename}.${media.extension}`

  return { medium, large, original }
}
