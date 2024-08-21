<script lang="ts">

import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  layout: AppLayout,
}
</script>

<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import { AnimeData } from '@/types/anime'
import { computedAsync } from '@vueuse/core'
import { useAnime } from '@/composables/useAniList'
import Banner from '@/Pages/Anime/Partials/Banner.vue'
import Metadata from '@/Pages/Anime/Partials/Metadata.vue'
import SpeedDial from '@/Pages/Anime/Partials/SpeedDial.vue'
import { Post } from '@/types'
import { TranslatableField } from '@/types/formHelper'
import dayjs from 'dayjs'
import InertiaLink from '@/Components/InertiaLink.vue'
import { VCard } from 'vuetify/components'
import { mdiPlay } from '@mdi/js'

type Episode = Post & {
  title: TranslatableField
  description: TranslatableField
  id: number
}

const props = defineProps<{
  anime: AnimeData & {
    posts: Episode[]
  }
}>()

const { animeApi } = useAnime()

const animeBwang = computedAsync(async () => {
  return await animeApi(props.anime.anilist_id, false)
})
</script>

<template>
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
      <v-col
        cols="12"
        md="4"
      >
        <Metadata
          v-if="animeBwang"
          :data="animeBwang"
        />
      </v-col>
      <v-col
        cols="12"
        md="8"
      >
        <v-row dense>
          <v-col
            v-for="episode in anime.posts"
            :key="episode.id"
            cols="4"
          >
            <v-hover v-slot="{isHovering, props}">
              <InertiaLink
                v-bind="props"
                :href="route('post.show',[ anime, episode])"
                :as="VCard"
                rounded="lg"
                variant="text"
                class="pa-2"
              >
                <v-img
                  rounded="lg"
                  aspect-ratio="16/9"
                  cover
                  src="https://images.unsplash.com/photo-1617233082866-9d9c58674778?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                >
                  <v-fade-transition>
                    <div
                      v-if="isHovering"
                      class="d-flex transition-fast-in-fast-out bg-grey-darken-4 v-card--reveal"
                      style="height: 100%;"
                    >
                      <v-icon :icon="mdiPlay" />
                    </div>
                  </v-fade-transition>
                </v-img>
                <div class="mx-2 mt-2">
                  <div class="text-subtitle-2 text-medium-emphasis">
                    {{ dayjs(episode.published_at).format('D MMM YYYY') }} &bull; {{ episode.author.name }}
                  </div>
                  <div class="text-subtitle-1">
                    {{ episode.title.en }}
                  </div>
                </div>
              </InertiaLink>
            </v-hover>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
  <!--  -->
</template>

<style scoped>
.v-card--reveal {
  align-items: center;
  bottom: 0;
  justify-content: center;
  opacity: .5;
  position: absolute;
  width: 100%;
}
</style>
