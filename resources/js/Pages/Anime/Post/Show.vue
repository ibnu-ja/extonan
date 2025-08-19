<script setup lang="ts">
import { Head, Link as InertiaLink, usePage } from '@inertiajs/vue3'
import { AnimeData, EpisodeData, Resource } from '@/types/anime'
import dayjs from 'dayjs'
import VerticalEpisodeCard from '@/Pages/Anime/Partials/VerticalEpisodeCard.vue'
import { BasePost, PageProps } from '@/types'
import { mdiOpenInNew } from 'mdi-js-es'
import { useDisplay } from 'vuetify'
import { CoverImage } from '@/types/anilist'
import { inject, nextTick, onMounted, useTemplateRef } from 'vue'
import SpeedDial from '@/Pages/Anime/Post/Partials/SpeedDial.vue'
import { useLanguages } from '@/composables/useLanguages'
import AppLayout from '@/Layouts/AppLayout.vue'
import { route as ziggyRoute } from 'ziggy-js'

defineOptions({
  name: 'AnimePostShow',
  layout: AppLayout,
})

type ResourceModel = BasePost & Resource & {
  id: number
}

type Episode = EpisodeData & {
  thumbnail: CoverImage | null
  slug: string
}

const props = defineProps<{
  anime: AnimeData & {
    posts: Episode[]
  }
  post: EpisodeData & {
    links: ResourceModel[]
    embeds: ResourceModel[]
    thumbnail: CoverImage | null
    metadata: Record<string, unknown>
    slug: string
  }
}>()

const { smAndUp } = useDisplay()

onMounted(() => {
  const activeEpisode = document.getElementById(props.post.slug)
  if (activeEpisode) activeEpisode.scrollIntoView()
})
// console.log(props.post.slug)
const { translate } = useLanguages()
let title: string
if (props.post.metadata.ep_no) {
  title = `${translate(props.anime.title)} - ${props.post.metadata.ep_no}`
} else {
  title = translate(props.post.title)
}

// Define a static type for known resolutions with their associated colors
type Resolution = '4K' | '1080p' | '720p' | '480p'

// Map resolution tokens to their corresponding standard resolution
const resolutionMap: { [key: string]: Resolution } = {
  '3840x2160': '4K',
  '1920x1080': '1080p',
  '1440x1080': '1080p',
  '1280x720': '720p',
  '960x720': '720p',
  '848x480': '480p',
}

// Color mapping for each resolution
const colorMap: { [key in Resolution]: string } = {
  '4K': 'none',
  '1080p': 'teal',
  '720p': 'indigo',
  '480p': 'deep-purple',
}

// Function to get the resolution string and corresponding color from the filename
function getResolutionTag(filename: string): { resolution: Resolution, color: string } | null {
  // Regex to match both "WIDTHxHEIGHT" or just "RESOLUTIONp" patterns
  const resolutionPattern = /(\d{3,4}x\d{3,4}|\d{3,4}p)/

  const match = filename.match(resolutionPattern)
  if (match) {
    const res = match[0]
    const standardResolution = res.includes('x') ? resolutionMap[res] : (res.toLowerCase() as Resolution)

    return standardResolution ? { resolution: standardResolution, color: colorMap[standardResolution] } : null
  }

  return null // Return null if no resolution is found
}

const page = usePage<PageProps>()

const route = inject('route') as typeof ziggyRoute

const episodeListRef = useTemplateRef<HTMLDivElement>('episode-list')
// I DONT KNOW WHAT HAPPENED HERE
const currentEpisodeRef = useTemplateRef<HTMLDivElement[]>('episode-' + props.post.id)

onMounted(async () => {
  await nextTick()
  if (episodeListRef.value && currentEpisodeRef.value) {
    currentEpisodeRef.value[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' })
  }
})
</script>

<template>
  <Head :title />

  <SpeedDial
    :anime-id="anime.id"
    :post-id="post.id"
  />
  <v-container :max-width="1800">
    <div>
      <div>
        <InertiaLink
          :href="anime.link"
          class="text-decoration-none mb-2 text-h6"
        >
          {{ translate(props.anime.title) }}
        </InertiaLink> <span v-if="post.metadata.ep_no">- {{ post.metadata.ep_no }}</span>
      </div>
      <h1 class="text-h4">
        {{ translate(props.post.title) }}
      </h1>
      <div
        v-if="post.is_published"
        class="text-medium-emphasis"
      >
        Published at {{ dayjs(post.published_at) }} by {{ post.author?.name }}
      </div>
    </div>
  </v-container>

  <v-divider />

  <v-container
    max-width="1800"
    class="sm:px-4 px-0"
  >
    <div class="flex flex-col lg:flex-row">
      <div class="md:basis-3/4">
        <v-img
          v-if="post.thumbnail"
          max-height="400"
          :src="post.thumbnail.extraLarge"
        />

        <p class="mb-4 px-2 sm:px-0">
          {{ post.description.en }}
        </p>

        <h3 class="text-h6 mb-4 px-2 sm:px-0">
          Download Links
        </h3>

        <v-expansion-panels
          v-if="post.links.length > 0"
          :rounded="smAndUp ? 'lg' : 0"
          multiple
          variant="accordion"
        >
          <v-expansion-panel
            v-for="postItem in post.links"
            :key="postItem.id"
            static
          >
            <v-expansion-panel-title>
              {{ postItem.name }}
              <v-chip
                v-if="getResolutionTag(postItem.name)"
                class="ml-2"
                density="compact"
                :color="getResolutionTag(postItem.name)?.color"
              >
                {{ getResolutionTag(postItem.name)?.resolution }}
              </v-chip>
            </v-expansion-panel-title>
            <v-expansion-panel-text>
              <v-list density="compact">
                <v-chip
                  v-for="(link, i) in postItem.value"
                  :key="i"
                  :href="link.value"
                  target="_blank"
                >
                  {{ link.name }}
                  <v-icon :icon="mdiOpenInNew" />
                </v-chip>
              </v-list>
            </v-expansion-panel-text>
          </v-expansion-panel>
        </v-expansion-panels>
        <div
          v-else
          class="px-2 sm:px-0"
        >
          No download links available.
        </div>
      </div>
      <div>
        <h4 class="text-h5 mb-4 px-2 sm:px-0">
          Other Episodes
        </h4>
        <div
          ref="episode-list"
          class="overflow-auto h-128"
        >
          <div
            v-for="episode in anime.posts"
            :id="'episode-' + episode.id"
            :key="episode.id"
            :ref="'episode-' + episode.id"
          >
            <VerticalEpisodeCard
              :show-action="!!page.props.auth.user"
              :active="episode.id == post.id"
              :image="episode.thumbnail?.extraLarge"
              :lazy-img="episode.thumbnail?.medium"
              :href="route('post.show', [anime, episode])"
              :title="episode.title.en!"
              :edit-url="route('post.edit', [anime, episode])"
              :delete-url="route('post.destroy', [anime, episode])"
              :is-published="anime.is_published"
            />
          </div>
        </div>
      </div>
    </div>
  </v-container>
</template>
