<script lang="ts" setup>
import { AnimeData } from '@/types/anime'
import dayjs from 'dayjs'
import calendar from 'dayjs/plugin/calendar'

import { register } from 'swiper/element/bundle'
import { useGradient } from '@/composables/useGradient'
import { VCard } from 'vuetify/components'
import InertiaLink from '@/Components/InertiaLink.vue'

register()

defineProps<{
  latestAnime: AnimeData[]
}>()

dayjs.extend(calendar)

const { gradient } = useGradient()

</script>

<template>
  <div class="relative">
    <swiper-container
      :loop="true"
    >
      <swiper-slide
        v-for="anime in latestAnime"
        :key="anime.id"
      >
        <InertiaLink
          :as="VCard"
          :link="false"
          :href="route('anime.show', anime)"
          :rounded="false"
          class="flex relative elevation-0 border-0 cursor-pointer"
          :elevation="0"
          height="440"
          variant="text"
        >
          <template #image>
            <v-img
              :gradient
              class="bg-blur"
              cover
              :src="anime.metadata.coverImage.extraLarge"
              :lazy-src="anime.metadata.coverImage.medium"
            />
          </template>
          <v-container
            class="p-2 p:sm-4 mt-auto h-[80%] md:h-[70%] w-full"
          >
            <div class="sm:p-4 gap-4 flex items-start">
              <v-img
                cover
                rounded
                width="200"
                class="flex-0-0"
                :src="anime.metadata.coverImage.extraLarge"
                :lazy-src="anime.metadata.coverImage.medium"
              />

              <div>
                <h3 class="text-h4 mb-2">
                  {{ anime.title.en }}
                </h3>
                <div class="flex flex-wrap gap-2 mb-4 ">
                  <v-chip
                    v-for="genre in anime.metadata.genres"
                    :key="genre"
                  >
                    {{ genre }}
                  </v-chip>
                </div>
                <p class="text-medium-emphasis text-subtitle-1 d-none d-md-block">
                  {{ anime.description.en }}
                </p>
              </div>
            </div>
          </v-container>
        </InertiaLink>
      </swiper-slide>
    </swiper-container>
  </div>
</template>

<style lang="scss">
.bg-blur {
  .v-img__img.v-img__img--cover  {
    filter: blur(5px);
    transform: scale(1.05); //remove edge
  }
}
</style>
