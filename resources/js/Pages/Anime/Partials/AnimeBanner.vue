<script setup lang="ts">

import AnimeDialog from '@/Pages/Anime/Partials/AnimeDialog.vue'
import { VChip } from 'vuetify/components'

import { Link as InertiaLink } from '@inertiajs/vue3'
import { AnimeData, EpisodeData } from '@/types/anime'
import { mdiAccountHardHat, mdiAccountMultiple, mdiPlay } from 'mdi-js-es'
import { useDisplay } from 'vuetify/framework'
import { inject, onMounted, ref } from 'vue'
import dayjs from 'dayjs'
import calendar from 'dayjs/plugin/calendar'
import { useLanguages } from '@/composables/useLanguages'
import { route as ziggyRoute } from 'ziggy-js'

type Props = {
  anime: AnimeData & {
    posts: EpisodeData[]
  }
  airingDate: string
}

defineProps<Props>()

const tab = defineModel<string | null | undefined>()

const { smAndDown, mdAndUp } = useDisplay()

const showingSynopsis = ref(false)

dayjs.extend(calendar)

const { translate, hasTranslation } = useLanguages()

const route = inject('route') as typeof ziggyRoute

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

const contentRef = ref<HTMLDivElement | null>(null)
const isClamped = ref(true)

const checkClamped = () => {
  const el = contentRef.value
  if (!el) return
  // Choose detection logic based on your CSS use:
  isClamped.value = el.offsetHeight < el.scrollHeight
}

onMounted(() => {
  checkClamped()
  window.addEventListener('resize', checkClamped)
})

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
  <div
    v-if="anime.metadata.bannerImage"
    class="bg-fixed bg-cover bg-center h-50 relative"
    :style="{ backgroundImage: `url(${anime.metadata.bannerImage})` }"
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
            ref="contentRef"
            class="text-medium-emphasis mb-2"
            :class="{'line-clamp-2': isClamped}"
          >
            {{ translate(anime.description) }}
          </div>
          <div v-if="smAndDown || isClamped">
            <v-btn
              class="block mx-auto md:mr-0"
              variant="text"
              @click="() => {
                if (smAndDown) showingSynopsis = !showingSynopsis
                else isClamped = !isClamped
              }"
            >
              {{ isClamped || smAndDown ? 'show more' : 'show less' }}
            </v-btn>
          </div>
        </div>

        <v-tabs
          v-model="tab"
          class="mt-auto"
          :items="tabs"
          :align-tabs="mdAndUp ? 'start' : 'center'"
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
</template>
