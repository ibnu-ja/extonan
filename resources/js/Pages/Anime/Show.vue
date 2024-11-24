<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import { AnimeData, EpisodeData } from '@/types/anime'
import Banner from '@/Pages/Anime/Partials/Banner.vue'
import HorizontalEpisodeCard from '@/Pages/Anime/Partials/HorizontalEpisodeCard.vue'
import dayjs from 'dayjs'
import { CoverImage } from '@/types/anilist'
import SpeedDial from '@/Pages/Anime/Partials/SpeedDial.vue'
import { useLanguages } from '@/composables/useLanguages'
import calendar from 'dayjs/plugin/calendar'
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({
  name: 'AnimeShow',
  layout: AppLayout,
})

dayjs.extend(calendar)

type Post = EpisodeData & {
  thumbnail: CoverImage | null
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  metadata: Record<string, any>
}

const props = defineProps<{
  anime: AnimeData & {
    posts: Post[]
  }
  canCreateEpisode: boolean
}>()

const { translate } = useLanguages()

const airingDate = dayjs({
  year: props.anime.metadata.startDate.year ?? undefined,
  month: props.anime.metadata.startDate.month ? props.anime.metadata.startDate.month - 1 : undefined,
  day: props.anime.metadata.startDate.day ? props.anime.metadata.startDate.day - 1 : undefined,
}).format('D MMM YYYY')

const episodeTitle = (post: Post): string => {
  if (post.metadata.ep_no != null) {
    if (post.metadata.post_type === 'tv') return `Ep. ${post.metadata.ep_no}: ${translate(post.title)}`
    if (post.metadata.post_type === 'bd') return `${translate(post.title)} (Ep. ${post.metadata.ep_no})`
  }

  return translate(post.title)
}

</script>

<template>
  <SpeedDial
    :can-delete="anime.can.delete"
    :can-edit="anime.can.update"
    :can-create-episode
    :anime-id="anime.id"
  />
  <Head :title="anime.title.en!" />

  <Banner
    :title="anime.title"
    :description="anime.description"
    :bg="anime.metadata.coverImage.extraLarge!"
    :cover-image="anime.metadata.coverImage!"
    :is-published="anime.is_published"
    :edit-url="route('anime.edit', anime)"
    :delete-url="route('anime.destroy', anime)"
    :permissions="anime.can"
  />

  <v-container class="px-0 sm:px-4">
    <div class="grid sm:grid-cols-12 gap-4 items-start">
      <div class="sm:col-span-8 md:col-span-9">
        <div class="px-4 sm:px-0 flex gap-2 mb-4 flex-wrap">
          <v-chip
            v-for="genre in anime.metadata.genres"
            :key="genre"
          >
            {{ genre }}
          </v-chip>
        </div>
        <h4 class="px-4 sm:px-0 text-h5">
          Episodes
        </h4>
        <div
          v-if="anime.posts.length > 0"
          class="grid md:grid-cols-2 lg:grid-cols-3"
        >
          <HorizontalEpisodeCard
            v-for="episode in anime.posts"
            :key="episode.id"
            :show-action="!!$page.props.auth.user"
            :image="episode.thumbnail?.extraLarge"
            :lazy-img="episode.thumbnail?.medium"
            :href="route('post.show', [anime, episode])"
            :permissions="episode.can"
            :delete-url="route('post.destroy', [anime, episode])"
            :edit-url="route('post.edit', [anime, episode])"
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
                {{ episode.author.name }}
              </div>
            </template>
          </HorizontalEpisodeCard>
        </div>
        <p v-else>
          No post.
        </p>
      </div>
      <div
        class="px-4 sm:px-0 sm:col-span-4 md:col-span-3"
      >
        <v-theme-provider theme="dark">
          <v-card
            variant="tonal"
            rounded="xl"
            class="bg-surface mb-4"
          >
            <v-card-text>
              <!-- metadata -->
              <div v-if="anime.metadata.startDate">
                <v-list-subheader>
                  Airing Date
                </v-list-subheader>
                <!--dayjs is 0 indexed-->
                {{ airingDate }}
              </div>
              <div v-if="anime.metadata.episodes">
                <v-list-subheader>
                  Episodes
                </v-list-subheader>
                {{ anime.metadata.episodes }}
              </div>
              <div v-if="anime.metadata.studios">
                <v-list-subheader>
                  Studio
                </v-list-subheader>
                <!--dayjs is 0 indexed-->

                <div class="flex flex-wrap gap-1">
                  <v-chip
                    v-for="{node: studio} in anime.metadata.studios.edges"
                    :key="studio.id"
                  >
                    {{ studio.name }}
                  </v-chip>
                </div>
              </div>
              <div v-if="anime.metadata.season && anime.metadata.seasonYear">
                <v-list-subheader>
                  Season
                </v-list-subheader>
                <v-chip>
                  {{ anime.metadata.season }} {{ anime.metadata.seasonYear }}
                </v-chip>
              </div>
            </v-card-text>
            <v-divider />
            <v-card-item title="Post Info" />
            <v-card-text>
              <div>
                <v-list-subheader>Created at</v-list-subheader>
                <span :title="dayjs(anime.created_at).toString()">
                  {{ dayjs(anime.created_at).calendar() }}</span>
              </div>
              <div>
                <v-list-subheader>
                  Last modified
                </v-list-subheader>
                <span :title="dayjs(anime.updated_at).toString()">
                  {{ dayjs(anime.updated_at).calendar() }}</span>
              </div>
              <div>
                <v-list-subheader>
                  Author
                </v-list-subheader>
                {{ anime.author.name }}
              </div>
              <div>
                <v-list-subheader>
                  Published at
                </v-list-subheader>
                <v-chip
                  v-if="!anime.is_published"
                  color="success"
                  variant="flat"
                >
                  Draft
                </v-chip>
                <span
                  v-else
                  :title="dayjs(anime.published_at).toString()"
                >
                  {{ dayjs(anime.published_at).calendar() }}
                </span>
              </div>
              <div
                v-if="anime.is_published"
              >
                <v-list-subheader>
                  Published by
                </v-list-subheader>
                {{ anime.publisher!.name }}
              </div>
            </v-card-text>
          </v-card>
        </v-theme-provider>
      </div>
    </div>
  </v-container>
</template>
