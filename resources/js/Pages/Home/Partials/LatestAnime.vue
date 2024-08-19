<script lang="ts" setup>
import { LatestAnimeItem } from '@/types/anime'
import dayjs from 'dayjs'
import calendar from 'dayjs/plugin/calendar'
import { computedAsync } from '@vueuse/core'
import { useAnime } from '@/composables/useAniList'
import { VCard } from 'vuetify/components'
import InertiaLink from '@/Components/InertiaLink.vue'
import { ref } from 'vue'
import { CoverImage } from '@/types/anilist'

const props = defineProps<{
  latestAnime: LatestAnimeItem[]
}>()

dayjs.extend(calendar)

const { homeAnimeApi } = useAnime()

const animeItems = computedAsync(async () => {
  const idJoined = props.latestAnime.map(item => item.anilist_id)

  // Wait for all promises to resolve
  const result = await homeAnimeApi(idJoined)

  return props.latestAnime.map((item) => {
    const match = result?.find(item2 => item2.id === item.anilist_id)

    const newVar: LatestAnimeItem & {
      coverImage: CoverImage | null
    } = match ? { ...item, coverImage: match.coverImage } : { ...item, coverImage: null }
    return newVar
  })
})

const carousel = ref(0)
</script>

<template>
  <v-carousel
    v-model="carousel"
    continuous
    height="auto"
    hide-delimiters
    show-arrows="hover"
  >
    <v-carousel-item
      v-for="anime in animeItems"
      :key="anime.id"
      height="auto"
    >
      <InertiaLink
        :href="anime.link"
        variant="text"
        :as="VCard"
      >
        <v-row>
          <v-col
            v-if="anime.coverImage"
            cols="3"
          >
            <v-img
              aspect-ratio="4/3"
              rounded
              :src="anime.coverImage.large"
              :lazy-src="anime.coverImage.medium"
            />
          </v-col>
          <v-col cols="9">
            <div class="px-0 pt-0">
              <h3 class="text-h4">
                {{ anime.title.en }}
              </h3>
            </div>
            <v-card-text class="px-0 pt-0 text-medium-emphasis">
              {{ anime.description.en }}
            </v-card-text>
          </v-col>
        </v-row>
      </Inertialink>
    </v-carousel-item>
  </v-carousel>
</template>
