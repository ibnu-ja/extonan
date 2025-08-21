<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import { AnimeData, EpisodeData } from '@/types/anime'
import dayjs from 'dayjs'
import SpeedDial from '@/Pages/Anime/Partials/SpeedDial.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useDisplay } from 'vuetify'
import AnimeMetadata from '@/Pages/Anime/Partials/AnimeMetadata.vue'
import Casts from '@/Pages/Anime/Partials/Casts.vue'
import objectSupport from 'dayjs/plugin/objectSupport'
import AnimeEpisodes from '@/Pages/Anime/Partials/AnimeEpisodes.vue'
import { shallowRef } from 'vue'
import AnimeBanner from '@/Pages/Anime/Partials/AnimeBanner.vue'

dayjs.extend(objectSupport)

defineOptions({
  name: 'AnimeShow',
  layout: AppLayout,
})

type Props = {
  anime: AnimeData & {
    posts: EpisodeData[]
  }
  canCreateEpisode: boolean
}

const props = defineProps<Props>()

const { mdAndUp } = useDisplay()

const tab = shallowRef('tab-1')

// noinspection JSUnusedGlobalSymbols
const airingDate = dayjs({
  year: props.anime.metadata.startDate.year ?? undefined,
  month: props.anime.metadata.startDate.month ? props.anime.metadata.startDate.month - 1 : undefined,
  day: props.anime.metadata.startDate.day ? props.anime.metadata.startDate.day - 1 : undefined,
}).format('D MMM YYYY')

</script>

<template>
  <SpeedDial
    :can-delete="anime.can.delete"
    :can-edit="anime.can.update"
    :can-create-episode
    :anime-id="anime.id"
  />

  <Head :title="anime.title.en!" /> <!-- Top: fixed background image -->

  <AnimeBanner
    v-model="tab"
    :anime
    :airing-date
  />
  <v-container
    max-width="1800"
    class="flex flex-row gap-4 p-0 sm:p-4"
  >
    <v-tabs-window
      v-model="tab"
      class="flex-1"
    >
      <v-tabs-window-item
        value="tab-1"
      >
        <AnimeEpisodes
          :posts="anime.posts"
          :anime-id="anime.id"
        />
      </v-tabs-window-item>
      <v-tabs-window-item
        value="tab-2"
      >
        <Casts
          v-if="anime.metadata"
          :data="anime.metadata"
          :show-title="false"
        />
      </v-tabs-window-item>
    </v-tabs-window>
    <div
      v-if="mdAndUp"
      class="w-[400px]"
    >
      <AnimeMetadata
        :metadata="anime.metadata"
        :airing-date
        :created_at="anime.created_at"
        :updated_at="anime.updated_at"
        :published_at="anime.published_at"
        :is_published="anime.is_published"
        :author="anime.author!"
        :publisher="anime.publisher!"
      />
    </div>
  </v-container>
</template>
