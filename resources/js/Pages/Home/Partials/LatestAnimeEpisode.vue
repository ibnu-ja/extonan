<script lang="ts" setup>

import dayjs from 'dayjs'
import { AnimeData, EpisodeData } from '@/types/anime'
import { CoverImage } from '@/types/anilist'
import { useLanguages } from '@/composables/useLanguages'
import { usePage } from '@inertiajs/vue3'
import { PageProps } from '@/types'
import emblaCarouselVue from 'embla-carousel-vue'
import HorizontalEpisodeCard from '@/Pages/Anime/Partials/HorizontalEpisodeCard.vue'

type Postable = EpisodeData & {
  postable: AnimeData
  thumbnail: CoverImage | null
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  metadata: Record<string, any>
}

type Props = {
  latestEpisodes: Postable[]
}

const { translate } = useLanguages()

const getTitle = (post: Postable): string => {
  if (post.metadata.post_type == 'tv') {
    return `EP ${post.metadata.ep_no} - ${translate(post.title)} `
  }
  return translate(post.title)
}

const page = usePage<PageProps>()
defineProps<Props>()

// const chunkedEp = computed(() => chunk(props.latestEpisodes, 3))

const [emblaRef] = emblaCarouselVue({ loop: false, slidesToScroll: 1, containScroll: 'trimSnaps', align: 'start' }, [])

</script>

<template>
  <v-container
    max-width="1800"
    class="px-2 sm:px-4 pb-0 mb-2 sm:mb-4"
  >
    <h1 class="text-h4 text-md-h3">
      Latest Episode
    </h1>
  </v-container>

  <v-container
    max-width="1800"
    class="px-2 sm:px-4 pb-0 mb-2 sm:mb-4"
  >
    <div
      ref="emblaRef"
      class="embla overflow-hidden"
    >
      <div class="flex">
        <div
          v-for="episode in latestEpisodes"
          :key="episode.id"
          class="embla__slide flex-none basis-1/4 min-w-0"
        >
          <HorizontalEpisodeCard
            :permissions="episode.can"
            :show-action="!!page.props.auth.user"
            class="mb-2"
            :image="episode.thumbnail?.extraLarge"
            :overhead="translate(episode.postable.title)"
            :lazy-img="episode.thumbnail?.medium"
            :title="getTitle(episode)"
            :href="route('post.show', [episode.postable, episode])"
            :edit-url="route('post.edit', [episode.postable, episode])"
            :delete-url="route('post.destroy', [episode.postable, episode])"
            :is-published="episode.is_published"
            :subtitle="`${dayjs(episode.published_at).format('D MMM YYYY')} &bull; ${episode.author.name}`"
          />
        </div>
      </div>
    </div>
  </v-container>
</template>
