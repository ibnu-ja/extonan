<script lang="ts" setup>
import dayjs from 'dayjs'
import { AnimeData, EpisodeData } from '@/types/anime'
import { CoverImage } from '@/types/anilist'
import { useLanguages } from '@/composables/useLanguages'
import { usePage } from '@inertiajs/vue3'
import { PageProps } from '@/types'
import emblaCarouselVue from 'embla-carousel-vue'
import HorizontalEpisodeCard from '@/Pages/Anime/Partials/HorizontalEpisodeCard.vue'
import { mdiChevronLeft, mdiChevronRight } from 'mdi-js-es'
import { onMounted, ref } from 'vue'

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
const page = usePage<PageProps>()
defineProps<Props>()

const getTitle = (post: Postable): string => {
  if (post.metadata.post_type == 'tv') {
    return `EP ${post.metadata.ep_no} - ${translate(post.title)} `
  }
  return translate(post.title)
}

// Embla
const canPrev = ref(false)
const canNext = ref(false)
const [emblaRef, emblaApi] = emblaCarouselVue(
  { loop: false, slidesToScroll: 1, containScroll: 'trimSnaps', align: 'start' },
  [],
)

const updateButtons = () => {
  if (!emblaApi.value) return
  canPrev.value = emblaApi.value.canScrollPrev()
  canNext.value = emblaApi.value.canScrollNext()
}

onMounted(() => {
  if (!emblaApi.value) return
  updateButtons()
  emblaApi.value.on('select', updateButtons)
  emblaApi.value.on('init', updateButtons)
})

const scrollPrev = () => emblaApi.value?.scrollPrev()
const scrollNext = () => emblaApi.value?.scrollNext()
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
    class="px-0 sm:px-4 pb-0 mb-2 sm:mb-4 relative group"
  >
    <!-- Carousel -->
    <div
      ref="emblaRef"
      class="embla overflow-hidden"
    >
      <div class="flex">
        <div
          v-for="episode in latestEpisodes"
          :key="episode.id"
          class="embla__slide flex-none basis-[83%] md:basis-[43%] lg:basis-[30%] xl:basis-[23%] min-w-0"
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
            :subtitle="`${dayjs(episode.published_at).format('D MMM YYYY')} &bull; ${episode.author?.name}`"
          />
        </div>
      </div>
    </div>

    <!-- Left Button -->
    <v-btn
      v-if="canPrev"
      icon
      :size="80"
      class="!absolute left-0 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
      @click="scrollPrev"
    >
      <v-icon
        :icon="mdiChevronLeft"
        :size="55"
      />
    </v-btn>

    <v-btn
      v-if="canNext"
      icon
      :size="80"
      class="!absolute right-0 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
      @click="scrollNext"
    >
      <v-icon
        :icon="mdiChevronRight"
        :size="55"
      />
    </v-btn>
  </v-container>
</template>
