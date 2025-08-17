<script lang="ts" setup>
import { Head, Link as InertiaLink, usePage } from '@inertiajs/vue3'
import { AnimeData, EpisodeData } from '@/types/anime'
import dayjs from 'dayjs'
import SpeedDial from '@/Pages/Anime/Partials/SpeedDial.vue'
import { useLanguages } from '@/composables/useLanguages'
import calendar from 'dayjs/plugin/calendar'
import AppLayout from '@/Layouts/AppLayout.vue'
import { inject, ref, shallowRef } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
import { PageProps } from '@/types'
import { useDisplay } from 'vuetify'
import { mdiAccountHardHat, mdiAccountMultiple, mdiPlay } from 'mdi-js-es'
import { VChip } from 'vuetify/components'
import AnimeDialog from '@/Pages/Anime/Partials/AnimeDialog.vue'
import AnimeMetadata from '@/Pages/Anime/Partials/AnimeMetadata.vue'
import HorizontalEpisodeCard from '@/Pages/Anime/Partials/HorizontalEpisodeCard.vue'
import Casts from '@/Pages/Anime/Partials/Casts.vue'
import objectSupport from 'dayjs/plugin/objectSupport'
import AnimeEpisodes from '@/Pages/Anime/Partials/AnimeEpisodes.vue'

dayjs.extend(objectSupport)

defineOptions({
  name: 'AnimeShow',
  layout: AppLayout,
})

dayjs.extend(calendar)

type Props = {
  anime: AnimeData & {
    posts: EpisodeData[]
  }
  canCreateEpisode: boolean
}

const props = defineProps<Props>()

const { translate, hasTranslation } = useLanguages()

const airingDate = dayjs({
  year: props.anime.metadata.startDate.year ?? undefined,
  month: props.anime.metadata.startDate.month ? props.anime.metadata.startDate.month - 1 : undefined,
  day: props.anime.metadata.startDate.day ? props.anime.metadata.startDate.day - 1 : undefined,
}).format('D MMM YYYY')

const route = inject('route') as typeof ziggyRoute

const bannerUrl = (props.anime?.metadata?.bannerImage) || (props.anime?.metadata?.coverImage?.extraLarge) || 'https://placehold.co/1200x400'

const tab = shallowRef<string | null>('tab-1')

const tabs = [
  {
    icon: mdiPlay,
    text: 'Episode',
    value: 'tab-1',
  },
  {
    icon: mdiAccountMultiple,
    text: 'Characters',
    value: 'tab-2',
  },
  {
    icon: mdiAccountHardHat,
    text: 'Staff',
    value: 'tab-3',
  },
]

const { smAndDown, mdAndUp } = useDisplay()

const clamped = ref(true)

const showingSynopsis = ref(false)

const page = usePage<PageProps>()

</script>

<template>
  <AnimeDialog
    v-if="smAndDown"
    v-model="showingSynopsis"
    :title="anime.title"
    :metadata="anime.metadata"
    :description="anime.description"
    :airing-date
    :created_at="anime.created_at"
    :updated_at="anime.updated_at"
    :published_at="anime.published_at"
    :is_published="anime.is_published"
    :author="anime.author!"
    :publisher="anime.publisher!"
  />
  <SpeedDial
    :can-delete="anime.can.delete"
    :can-edit="anime.can.update"
    :can-create-episode
    :anime-id="anime.id"
  />
  <Head :title="anime.title.en!" /> <!-- Top: fixed background image -->

  <div
    v-if="anime.metadata.bannerImage"
    class="bg-fixed bg-cover bg-center h-50 relative"
    :style="{ backgroundImage: `url(${bannerUrl})` }"
  />
  <v-container max-width="1800">
    <div class="flex md:flex-row flex-col gap-4">
      <div
        class="w-[150px] md:w-[250px] mx-auto md:mx-0"
        :class="{'-mt-40 md:-mt-20': anime.metadata.bannerImage}"
      >
        <v-img
          style="cursor: pointer;"
          rounded
          :src="anime.metadata.coverImage.extraLarge"
          :lazy-src="anime.metadata.coverImage.medium"
        >
          <v-dialog
            close-on-content-click
            fullscreen
            activator="parent"
          >
            <v-img
              height="100%"
              :src="anime.metadata.coverImage.extraLarge"
              :lazy-src="anime.metadata.coverImage.medium"
            />
          </v-dialog>
        </v-img>
      </div>
      <div class="flex flex-col w-full">
        <div>
          <h3 class="text-h4 mb-2">
            {{ translate(anime.title) }}
          </h3>
          <v-chip
            v-if="!anime.is_published"
            color="success"
            variant="flat"
          >
            Draft
          </v-chip>
        </div>
        <h3
          v-if="mdAndUp"
          class="text-h5 mb-2 text-medium-emphasis"
        >
          {{ anime.title.native }}
        </h3>
        <h3
          v-if="mdAndUp && hasTranslation(anime.title, false)"
          class="text-h5 mb-2 text-medium-emphasis"
        >
          {{ anime.title.romaji }}
        </h3>
        <!--TODO render wysiwyg-->

        <div>
          <div class="flex gap-2 mb-2 flex-wrap">
            <InertiaLink
              v-for="genre in anime.metadata.genres"
              :key="genre"
              :as="VChip"
              :href="route('anime.index', { filter: {genre_in: genre} })"
            >
              {{ genre }}
            </InertiaLink>
          </div>
          <div
            class="text-medium-emphasis"
            :class="{'line-clamp-3': clamped}"
          >
            {{ translate(anime.description) }}
          </div>
          <div>
            <v-btn
              class="block mx-auto md:mr-0"
              variant="text"
              @click="() => {
                if (smAndDown) showingSynopsis = !showingSynopsis
                else clamped = !clamped
              }"
            >
              {{ clamped ? 'show more' : 'show less' }}
            </v-btn>
          </div>
        </div>

        <v-tabs
          v-model="tab"
          class="mt-auto"
          :items="tabs"
          align-tabs="start"
        >
          <template #tab="{ item }">
            <v-tab
              :prepend-icon="item.icon"
              :text="item.text"
              :value="item.value"
              class="text-none"
            />
          </template>
        </v-tabs>
      </div>
    </div>
  </v-container>

  <v-container
    max-width="1800"
    class="flex flex-row gap-4"
  >
    <v-tabs-window
      v-model="tab"
      class="flex-1"
    >
      <v-tabs-window-item
        value="tab-1"
      >
        <AnimeEpisodes
          :posts="anime.posts"
          :anime-id="anime.id"
        />
      </v-tabs-window-item>
      <v-tabs-window-item
        value="tab-2"
      >
        <Casts
          v-if="anime.metadata"
          :data="anime.metadata"
          :show-title="false"
        />
      </v-tabs-window-item>
    </v-tabs-window>
    <div
      v-if="mdAndUp"
      class="w-[400px]"
    >
      <AnimeMetadata
        :metadata="anime.metadata"
        :airing-date
        :created_at="anime.created_at"
        :updated_at="anime.updated_at"
        :published_at="anime.published_at"
        :is_published="anime.is_published"
        :author="anime.author!"
        :publisher="anime.publisher!"
      />
    </div>
  </v-container>
</template>
