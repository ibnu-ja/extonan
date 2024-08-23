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

type Postable = EpisodeData & {
  postable: AnimeData
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
  <div style="position: relative">
    <LatestAnime :latest-anime />
    <div
      class="absolute top-4 md:top-16 left-0 w-full z-[5]"
    >
      <v-container>
        <h1 class="text-h4 text-md-h3 mb-4">
          Latest Anime
        </h1>
      </v-container>
    </div>
  </div>
  <v-container class="px-0 sm:px-4">
    <v-row dense>
      <v-col
        v-for="episode in latestEpisodes"
        :key="episode.id"
        cols="12"
        md="6"
      >
        <VerticalEpisodeCard
          :image="episode.postable.metadata.coverImage.extraLarge"
          :lazy-img="episode.postable.metadata.coverImage.medium"
          :href="route('post.show', [episode.postable, episode])"
          :title="`${episode.postable.title.en} - ${episode.title.en}`"
          :subtitle="`${dayjs(episode.published_at).format('D MMM YYYY')} &bull;`"
        />
      </v-col>
    </v-row>
  </v-container>
</template>
