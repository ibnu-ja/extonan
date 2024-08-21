<script lang="ts">

import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  layout: AppLayout,
}
</script>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { AnimeData, EpisodeData } from '@/types/anime'
import dayjs from 'dayjs'
import { AnimeMediaAutofillResponse } from '@/types/anilist'
import InertiaLink from '@/Components/InertiaLink.vue'
import VerticalAnimeCard from '@/Pages/Anime/Partials/VerticalAnimeCard.vue'

defineProps<{
  anime: AnimeData & {
    metadata: AnimeMediaAutofillResponse
  }
  post: EpisodeData
}>()

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

  <v-container class="px-sm-4 px-0">
    <v-row>
      <v-col
        cols="12"
        md="8"
      >
        <p>{{ post.description.en }}</p>
      </v-col>
      <v-col
        cols="12"
        md="4"
      >
        <v-list-item-subtitle>
          Next episode
        </v-list-item-subtitle>
        <VerticalAnimeCard
          :anime
          :episode="post"
        />
      </v-col>
    </v-row>
  </v-container>
</template>
