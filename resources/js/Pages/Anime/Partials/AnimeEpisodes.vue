<script setup lang="ts">

import HorizontalEpisodeCard from '@/Pages/Anime/Partials/HorizontalEpisodeCard.vue'
import { EpisodeData } from '@/types/anime'
import dayjs from 'dayjs'
import { useLanguages } from '@/composables/useLanguages'
import { usePage } from '@inertiajs/vue3'
import { PageProps } from '@/types'

type Props = {
  posts: EpisodeData[]
  animeId: number
}

const { translate } = useLanguages()

defineProps<Props>()

const episodeTitle = (post: EpisodeData): string => {
  if (post.metadata.ep_no != null) {
    if (post.metadata.post_type === 'tv') return `Ep. ${post.metadata.ep_no}: ${translate(post.title)}`
    if (post.metadata.post_type === 'bd') return `${translate(post.title)} (Ep. ${post.metadata.ep_no})`
  }

  return translate(post.title)
}

const page = usePage<PageProps>()
</script>
<template>
  <div>
    <div
      v-if="posts.length > 0"
      class="grid md:grid-cols-2 lg:grid-cols-3"
    >
      <HorizontalEpisodeCard
        v-for="episode in posts"
        :key="episode.id"
        :show-action="!!page.props.auth.user"
        :image="episode.thumbnail?.extraLarge"
        :lazy-img="episode.thumbnail?.medium"
        :href="route('post.show', [animeId, episode])"
        :permissions="episode.can"
        :delete-url="route('post.destroy', [animeId, episode])"
        :edit-url="route('post.edit', [animeId, episode])"
        :is-published="episode.is_published"
      >
        <template #content>
          <div class="text-subtitle-1 list-title">
            {{ episodeTitle(episode) }}
          </div>
          <div
            class="text-subtitle-2 text-medium-emphasis"
          >
            <template
              v-if="!episode.is_published"
            >
              <span
                class="text-success"
              >
                Draft
              </span> by
            </template>
            <template v-else>
              {{ dayjs(episode.published_at).format('D MMM YYYY') }} &bull;
            </template>
            {{ episode.author?.name }}
          </div>
        </template>
      </HorizontalEpisodeCard>
    </div>
    <p v-else>
      No post.
    </p>
  </div>
</template>
