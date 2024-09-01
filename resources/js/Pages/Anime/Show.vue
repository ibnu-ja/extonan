<script lang="ts">

import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  layout: AppLayout,
}
</script>

<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import { AnimeData, EpisodeData } from '@/types/anime'
import { computedAsync } from '@vueuse/core'
import { useAnime } from '@/composables/useAniList'
import Banner from '@/Pages/Anime/Partials/Banner.vue'
import Metadata from '@/Pages/Anime/Partials/Metadata.vue'
import HorizontalEpisodeCard from '@/Pages/Anime/Partials/HorizontalEpisodeCard.vue'
import dayjs from 'dayjs'
import { CoverImage } from '@/types/anilist'
import SpeedDial from '@/Pages/Anime/Partials/SpeedDial.vue'

type Post = EpisodeData & {
  thumbnail: CoverImage | null
}

const props = defineProps<{
  anime: AnimeData & {
    posts: Post[]
  }
  canCreateEpisode: boolean
}>()

const { animeApi } = useAnime()

const animeBwang = computedAsync(async () => {
  return await animeApi(props.anime.anilist_id, false)
})

</script>

<template>
  <SpeedDial
    :can-create-episode
    :anime-id="anime.id"
  />
  <Head :title="anime.title.en!" />

  <Banner
    v-if="animeBwang"
    :genres="anime.metadata.genres"
    :title="anime.title"
    :description="anime.description"
    :bg="animeBwang?.coverImage.extraLarge!"
    :cover-image="animeBwang?.coverImage!"
    :is-published="anime.is_published"
    :edit-url="route('anime.edit', anime)"
    :delete-url="route('anime.destroy', anime)"
    :permissions="anime.can"
  />

  <v-container class="px-0 sm:px-4">
    <v-row>
      <v-col
        cols="12"
        sm="4"
      >
        <Metadata
          v-if="animeBwang"
          :data="animeBwang"
        />
      </v-col>
      <v-col
        cols="12"
        sm="8"
      >
        <v-row
          v-if="anime.posts.length > 0"
          dense
        >
          <v-col
            v-for="episode in anime.posts"
            :key="episode.id"
            cols="12"
            sm="6"
          >
            <HorizontalEpisodeCard
              :show-action="!!$page.props.auth.user"
              :image="episode.thumbnail?.extraLarge"
              :lazy-img="episode.thumbnail?.medium"
              :href="route('post.show', [anime, episode])"
              :title="episode.title.en!"
              :permissions="episode.can"
              :subtitle="`${dayjs(episode.published_at).format('D MMM YYYY')} &bull; ${episode.author.name}`"
              :delete-url="route('post.destroy', [anime, episode])"
              :edit-url="route('post.edit', [anime, episode])"
              :is-published="episode.is_published"
            />
          </v-col>
        </v-row>
        <p v-else>
          No post.
        </p>
      </v-col>
    </v-row>
  </v-container>
</template>
