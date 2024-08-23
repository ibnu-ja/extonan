<script lang="ts">

import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  layout: AppLayout,
}
</script>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { AnimeData, EpisodeData, Resource } from '@/types/anime'
import dayjs from 'dayjs'
import InertiaLink from '@/Components/InertiaLink.vue'
import VerticalAnimeCard from '@/Pages/Anime/Partials/VerticalEpisodeCard.vue'
import { Post } from '@/types'
import { mdiCircleSmall, mdiOpenInNew } from '@mdi/js'
import { useDisplay } from 'vuetify'

type ResourceModel = Post & Resource & {
  id: number
}

defineProps<{
  anime: AnimeData
  post: EpisodeData & {
    links: ResourceModel[]
    embeds: ResourceModel[]
  }
}>()

const { smAndUp } = useDisplay()

</script>

<template>
  <Head title="Bwang" />

  <v-container>
    <div>
      <InertiaLink
        :href="anime.link"
        class="text-decoration-none mb-2 text-h6"
      >
        {{ anime.title.en }}
      </InertiaLink>
      <h1 class="text-h4">
        {{ post.title.en }}
      </h1>
      <div
        v-if="post.is_published"
        class="text-medium-emphasis"
      >
        Published at {{ dayjs(post.published_at) }} by {{ post.author.name }}
      </div>
    </div>
  </v-container>

  <v-divider />

  <v-container class="sm:px-4 px-0">
    <v-row>
      <v-col
        cols="12"
        md="8"
      >
        <p>{{ post.description.en }}</p>

        <h3 class="text-h6 mb-4">
          Download Links
        </h3>

        <v-expansion-panels
          :rounded="smAndUp ? 'lg' : 0"
          multiple
          variant="accordion"
        >
          <v-expansion-panel
            v-for="postItem in post.links"
            :key="postItem.id"
            static
          >
            <v-expansion-panel-title>{{ postItem.name }}</v-expansion-panel-title>
            <v-expansion-panel-text>
              <v-list density="compact">
                <v-list-item
                  v-for="link in postItem.value"
                  :key="link.id"
                  :prepend-icon="mdiCircleSmall"
                >
                  <a
                    :href="link.value"
                    target="_blank"
                  >{{ link.name }} <v-icon :icon="mdiOpenInNew" /></a>
                </v-list-item>
              </v-list>
            </v-expansion-panel-text>
          </v-expansion-panel>
        </v-expansion-panels>
      </v-col>
      <v-col
        cols="12"
        md="4"
      >
        <v-list-item-subtitle>
          Next episode
        </v-list-item-subtitle>
        <VerticalAnimeCard
          :image="anime.metadata.coverImage.extraLarge"
          :lazy-img="anime.metadata.coverImage.medium"
          :href="route('post.show', [anime, post])"
          :title="post.title.en!"
        />
      </v-col>
    </v-row>
  </v-container>
</template>
