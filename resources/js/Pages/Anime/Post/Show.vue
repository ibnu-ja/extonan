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
import InertiaLink from '@/Components/InertiaLink'
import VerticalEpisodeCard from '@/Pages/Anime/Partials/VerticalEpisodeCard.vue'
import { Post } from '@/types'
import { mdiOpenInNew } from '@mdi/js'
import { useDisplay } from 'vuetify'
import { CoverImage } from '@/types/anilist'
import { onMounted } from 'vue'
import SpeedDial from '@/Pages/Anime/Post/Partials/SpeedDial.vue'

type ResourceModel = Post & Resource & {
  id: number
}

type Episode = EpisodeData & {
  thumbnail: CoverImage | null
  slug: string
}

const props = defineProps<{
  anime: AnimeData & {
    posts: Episode[]
  }
  post: EpisodeData & {
    links: ResourceModel[]
    embeds: ResourceModel[]
    thumbnail: CoverImage | null
    slug: string
  }
}>()

const { smAndUp } = useDisplay()

onMounted(() => {
  const activeEpisode = document.getElementById(props.post.slug)
  if (activeEpisode) activeEpisode.scrollIntoView()
})
// console.log(props.post.slug)
</script>

<template>
  <Head :title="anime.title.en!" />

  <v-container>
    <SpeedDial
      :anime-id="anime.id"
      :post-id="post.id"
    />
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
        <v-img
          v-if="post.thumbnail"
          class="w-full sm:w-[75%] mx-auto mb-4"
          :src="post.thumbnail.extraLarge"
        />

        <p class="mb-4 px-2 sm:px-0">
          {{ post.description.en }}
        </p>

        <h3 class="text-h6 mb-4 px-2 sm:px-0">
          Download Links
        </h3>

        <v-expansion-panels
          v-if="post.links.length > 0"
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
                <v-chip
                  v-for="(link, i) in postItem.value"
                  :key="i"
                  :href="link.value"
                  target="_blank"
                >
                  {{ link.name }} <v-icon :icon="mdiOpenInNew" />
                </v-chip>
              </v-list>
            </v-expansion-panel-text>
          </v-expansion-panel>
        </v-expansion-panels>
        <div
          v-else
          class="px-2 sm:px-0"
        >
          No download links available.
        </div>
      </v-col>
      <v-col
        cols="12"
        md="4"
      >
        <h3 class="text-h6 mb-4 px-2 sm:px-0">
          Other Episodes
        </h3>
        <div class="overflow-auto h-128">
          <VerticalEpisodeCard
            v-for="episode in anime.posts"
            :id="episode.slug"
            :key="episode.id"
            :show-action="!!$page.props.auth.user"
            :active="episode.id == post.id"
            class="mb-2"
            :image="episode.thumbnail?.extraLarge"
            :lazy-img="episode.thumbnail?.medium"
            :href="route('post.show', [anime, episode])"
            :title="episode.title.en!"
            :edit-url="route('post.edit', [anime, episode])"
            :delete-url="route('post.destroy', [anime, episode])"
            :is-published="anime.is_published"
          />
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>
