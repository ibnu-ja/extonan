<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import { AnimeData, EpisodeData } from '@/types/anime'
import LatestAnime from '@/Pages/Home/Partials/LatestAnime.vue'
import VerticalEpisodeCard from '@/Pages/Anime/Partials/VerticalEpisodeCard.vue'
import dayjs from 'dayjs'
import { CoverImage } from '@/types/anilist'
import { useLanguages } from '@/composables/useLanguages'
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({
  name: 'Home',
  layout: AppLayout,
})

type Postable = EpisodeData & {
  postable: AnimeData
  thumbnail: CoverImage | null
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  metadata: Record<string, any>
}

defineProps<{
  laravelVersion: string
  phpVersion: string
  latestAnime: AnimeData[]
  latestEpisodes: Postable[]
}>()

const getTitle = (post: Postable): string => {
  if (post.metadata.post_type == 'tv') {
    return `${translate(post.postable.title)} - ${post.metadata.ep_no}`
  }
  return translate(post.title)
}

const { translate } = useLanguages()

</script>

<template>
  <Head title="Home" />
  <section style="position: relative">
    <template v-if="latestAnime.length > 0">
      <LatestAnime :latest-anime />
      <div
        class="absolute top-4 md:top-16 left-0 w-full z-[5]"
      >
        <v-container class="pb-0 mb-2 sm:mb-4">
          <h1 class="text-h4 text-md-h3">
            Latest Anime
          </h1>
        </v-container>
      </div>
    </template>
    <v-container v-else>
      Add anime to show latest anime slider.
    </v-container>
  </section>
  <section class="mt-4 sm:mt-8">
    <template v-if="latestEpisodes.length > 0">
      <v-container class="px-2 sm:px-4 pb-0 mb-2 sm:mb-4">
        <h1 class="text-h4 text-md-h3">
          Latest Episode
        </h1>
      </v-container>
      <v-container class="px-0 sm:px-4 pt-0">
        <v-row dense>
          <v-col
            v-for="episode in latestEpisodes"
            :key="episode.id"
            cols="12"
            md="6"
          >
            <VerticalEpisodeCard
              :key="episode.id"
              :permissions="episode.can"
              :show-action="!!$page.props.auth.user"
              class="mb-2"
              :image="episode.thumbnail?.extraLarge"
              :lazy-img="episode.thumbnail?.medium"
              :title="getTitle(episode)"
              :href="route('post.show', [episode.postable, episode])"
              :edit-url="route('post.edit', [episode.postable, episode])"
              :delete-url="route('post.destroy', [episode.postable, episode])"
              :is-published="episode.is_published"
              :subtitle="`${dayjs(episode.published_at).format('D MMM YYYY')} &bull; ${episode.author.name}`"
            />
          </v-col>
        </v-row>
      </v-container>
    </template>
    <v-container v-else>
      Add anime post to show latest episode data.
    </v-container>
  </section>
</template>
