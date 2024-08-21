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
import { mdiDotsVertical, mdiPencil, mdiPlay, mdiSend } from '@mdi/js'
import { useDisplay } from 'vuetify'
import { mdiDelete } from '@mdi/js/commonjs/mdi'

type Episode = Post & {
  title: TranslatableField
  description: TranslatableField
  id: number
}

const props = defineProps<{
  anime: AnimeData & {
    posts: Episode[]
  }
  canCreate: boolean
}>()

const { animeApi } = useAnime()

const animeBwang = computedAsync(async () => {
  return await animeApi(props.anime.anilist_id, false)
})

const { smAndUp } = useDisplay()
</script>

<template>
  <SpeedDial
    v-if="canCreate"
    :anime
  />
  <Head :title="anime.title.en!" />

  <Banner
    v-if="animeBwang"
    :title="anime.title.en!"
    :bg="animeBwang?.coverImage.extraLarge!"
    :cover-image="animeBwang?.coverImage!"
  />

  <v-container class="px-0 px-sm-4">
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
            cols="12"
            md="4"
          >
            <v-hover v-slot="{isHovering, props: propsss}">
              <InertiaLink
                v-bind="propsss"
                :href="route('post.show',[ anime, episode])"
                :as="VCard"
                variant="text"
                class="pa-2"
                :rounded="smAndUp ? 'lg' : false"
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
                <v-list-item class="px-0">
                  <div class="mx-2 mt-2">
                    <div class="text-subtitle-2 text-medium-emphasis">
                      {{ dayjs(episode.published_at).format('D MMM YYYY') }} &bull; {{ episode.author.name }}
                    </div>
                    <div class="text-subtitle-1">
                      {{ episode.title.en }}
                    </div>
                  </div>
                  <template
                    v-if="episode.can.publish || episode.can.update || episode.can.delete"
                    #append
                  >
                    <v-menu>
                      <template #activator="{ props: propss }">
                        <v-btn
                          density="comfortable"
                          variant="plain"
                          :icon="mdiDotsVertical"
                          v-bind="propss"
                          @click.prevent
                        />
                      </template>
                      <v-list density="comfortable">
                        <v-list-item
                          :prepend-icon="mdiSend"
                          title="Publish"
                        >
                          <template #prepend>
                            <v-icon
                              :icon="mdiSend"
                              color="primary"
                            />
                          </template>
                        </v-list-item>
                        <v-list-item
                          title="Edit"
                        >
                          <template #prepend>
                            <v-icon
                              :icon="mdiPencil"
                              color="secondary"
                            />
                          </template>
                        </v-list-item>
                        <v-list-item
                          title="Delete"
                        >
                          <template #prepend>
                            <v-icon
                              :icon="mdiDelete"
                              color="error"
                            />
                          </template>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </template>
                </v-list-item>
              </InertiaLink>
            </v-hover>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
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
