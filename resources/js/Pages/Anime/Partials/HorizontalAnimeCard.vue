<script lang="ts" setup>

import { useDisplay } from 'vuetify'
import { VCard } from 'vuetify/components'
import { mdiPlay } from '@mdi/js'
import dayjs from 'dayjs'
import { AnimeData, EpisodeData } from '@/types/anime'
import InertiaLink from '@/Components/InertiaLink.vue'
import ItemListTitle from '@/Pages/Anime/Partials/ItemListTitle.vue'

defineProps<{
  episode: EpisodeData
  anime: AnimeData
}>()

const { smAndUp } = useDisplay()

</script>

<template>
  <v-hover v-slot="{isHovering, props: propsss}">
    <InertiaLink
      v-bind="propsss"
      :href="route('post.show', [anime, episode])"
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
      <ItemListTitle
        class="mt-2"
        :permissions="episode.can"
        :overhead="`${dayjs(episode.published_at).format('D MMM YYYY')} &bull; ${episode.author.name}`"
        :title="episode.title"
      />
    </InertiaLink>
  </v-hover>
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
