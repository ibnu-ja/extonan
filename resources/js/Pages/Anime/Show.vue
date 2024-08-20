<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { AnimeData } from '@/types/anime'
import { computedAsync } from '@vueuse/core'
import { useAnime } from '@/composables/useAniList'
import Banner from '@/Pages/Anime/Partials/Banner.vue'
import Metadata from '@/Pages/Anime/Partials/Metadata.vue'
import SpeedDial from '@/Pages/Anime/Partials/SpeedDial.vue'

const props = defineProps<{
  anime: AnimeData
}>()

const { animeApi } = useAnime()

const animeBwang = computedAsync(async () => {
  return await animeApi(props.anime.anilist_id, false)
})
</script>

<template>
  <AppLayout>
    <SpeedDial :anime />
    <Head :title="anime.title.en!" />

    <Banner
      v-if="animeBwang"
      :title="anime.title.en!"
      :bg="animeBwang?.coverImage.extraLarge!"
      :cover-image="animeBwang?.coverImage!"
    />

    <v-container>
      <v-row>
        <v-col cols="4">
          <Metadata
            v-if="animeBwang"
            :data="animeBwang"
          />
        </v-col>
        <v-col cols="8">
          episodes
        </v-col>
      </v-row>
    </v-container>
  </AppLayout>
  <!--  -->
</template>
