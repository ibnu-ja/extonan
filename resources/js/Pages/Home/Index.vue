<script lang="ts">

import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  layout: AppLayout,
}
</script>

<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import { AnimeData, EpisodeData } from '@/types/anime'
import LatestAnime from '@/Pages/Home/Partials/LatestAnime.vue'
import VerticalEpisodeCard from '@/Pages/Anime/Partials/VerticalEpisodeCard.vue'
import dayjs from 'dayjs'
import { CoverImage } from '@/types/anilist'

type Postable = EpisodeData & {
  postable: AnimeData
  thumbnail: CoverImage | null
}

defineProps<{
  canLogin?: boolean
  canRegister?: boolean
  laravelVersion: string
  phpVersion: string
  latestAnime: AnimeData[]
  latestEpisodes: Postable[]
}>()

</script>

<template>
  <Head title="Home" />
  <section style="position: relative">
    <LatestAnime :latest-anime />
    <div
      class="absolute top-4 md:top-16 left-0 w-full z-[5]"
    >
      <v-container class="px-2 sm:px-4 pb-0 mb-2 sm:mb-4">
        <h1 class="text-h4 text-md-h3">
          Latest Episode
        </h1>
      </v-container>
    </div>
  </section>
  <section class="mt-4 sm:mt-8">
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
            :image="episode.thumbnail? episode.thumbnail.extraLarge : undefined"
            :lazy-img="episode.thumbnail? episode.thumbnail.medium : undefined"
            :href="route('post.show', [episode.postable, episode])"
            :title="`${episode.postable.title.en} - ${episode.title.en}`"
            :subtitle="`${dayjs(episode.published_at).format('D MMM YYYY')} &bull; ${episode.author.name}`"
          />
        </v-col>
      </v-row>
    </v-container>
  </section>
</template>
