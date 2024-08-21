<script lang="ts">

import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  layout: AppLayout,
}
</script>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { AnimeData } from '@/types/anime'
import { Post } from '@/types'
import { TranslatableField } from '@/types/formHelper'
import dayjs from 'dayjs'
import { AnimeMediaAutofillResponse } from '@/types/anilist'
import { mdiPlay } from '@mdi/js'

defineProps<{
  anime: AnimeData & {
    metadata: AnimeMediaAutofillResponse
  }
  post: Post & {
    title: TranslatableField
    description: TranslatableField
  }
}>()

</script>

<template>
  <Head title="Bwang" />

  <v-container>
    <div>
      <div class="mb-2 text-h6">
        bwang
      </div>
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

  <v-container>
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
        <v-hover v-slot="{isHovering, props}">
          <v-card
            class="d-flex gap-2"
            variant="text"
            v-bind="props"
          >
            <v-img
              v-if="anime.metadata.coverImage"
              width="150"
              class="rounded align-self-center flex-0-0 cursor-pointer"
              :src="anime.metadata.coverImage.extraLarge"
              :lazy-src="anime.metadata.coverImage.medium"
              :aspect-ratio="16/9"
              cover
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
            <div
              class="col-span-4"
              style="flex: 1; line-clamp: 2"
            >
              <div
                class="list-title font-weight-medium"
              >
                {{ post.title.en }} Lorem ipsum dolor sit amet, consectetur adipisicing elit.
              </div>
            </div>
          </v-card>
        </v-hover>
      </v-col>
    </v-row>
  </v-container>
</template>

<style>
.v-card--reveal {
  align-items: center;
  bottom: 0;
  justify-content: center;
  opacity: .5;
  position: absolute;
  width: 100%;
}

.list-title {
  display: block;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 4;
  line-clamp: 4;
  -webkit-box-orient: vertical;
}
</style>
