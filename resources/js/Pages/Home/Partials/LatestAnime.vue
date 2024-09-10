<script lang="ts" setup>
import { AnimeData } from '@/types/anime'
import dayjs from 'dayjs'
import calendar from 'dayjs/plugin/calendar'

import { register } from 'swiper/element/bundle'
import { useGradient } from '@/composables/useGradient'
import { VCard } from 'vuetify/components'
import { useLanguages } from '@/composables/useLanguages'
import { mdiCircle } from '@mdi/js'
import { SwiperContainer } from 'swiper/element'
import { ref } from 'vue'
import { useDisplay } from 'vuetify'
import InertiaLink from '@/Components/InertiaLink'

register()

defineProps<{
  latestAnime: AnimeData[]
}>()

dayjs.extend(calendar)

const { gradient } = useGradient()

const { mdAndUp } = useDisplay()

const { translate } = useLanguages()

const swiper = ref<SwiperContainer>()

const onTransitionEnd = () => {
  console.log('slide changed', swiper.value?.swiper.realIndex)
  slide.value = swiper.value?.swiper.realIndex
}

const slide = ref(0)

const pressButton = (value) => {
  swiper.value?.swiper.slideTo(value)
}
</script>

<template>
  <div class="relative">
    <swiper-container
      ref="swiper"
      :loop="true"
      @swiperslidechange="onTransitionEnd"
    >
      <!--eslint-disable vue/no-deprecated-slot-attribute-->
      <div
        v-if="latestAnime.length > 1"
        slot="container-end"
        class="text-center bg-transparent"
      >
        <v-item-group
          v-model="slide"
          mandatory
        >
          <v-item
            v-for="(anime, n) in latestAnime"
            :key="anime.id"
            v-slot="{isSelected}"
          >
            <v-btn
              :active="isSelected"
              size="small"
              variant="text"
              class="v-carousel__controls__item swiper-pagination"
              :icon="mdiCircle"
              @click="pressButton(n-1)"
            />
          </v-item>
        </v-item-group>
      </div>
      <!--eslint-enable-->
      <swiper-slide
        v-for="anime in latestAnime"
        :key="anime.id"
      >
        <InertiaLink
          :as="VCard"
          :link="false"
          :href="route('anime.show', anime)"
          :rounded="false"
          class="h-[330px] md:h-[400px] flex relative elevation-0 border-0 cursor-pointer"
          :elevation="0"
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
            class="mt-auto h-[70%] md:h-[65%] w-full"
          >
            <div class="md:p-4 gap-4 flex items-start">
              <v-img
                cover
                rounded
                class="flex-0-0 w-[30vw] sm:w-[20vw] md:w-[170px]"
                :src="anime.metadata.coverImage.extraLarge"
                :lazy-src="anime.metadata.coverImage.medium"
              />

              <div class="h-[330px]">
                <h3 class="text-h4 mb-4">
                  {{ translate(anime.title) }} {{ i }}
                </h3>

                <div
                  v-if="mdAndUp"
                  class="line-clamp-3 mb-4"
                >
                  <p class="text-medium-emphasis text-subtitle-1">
                    {{ translate(anime.description) }}
                  </p>
                </div>
                <div class="flex flex-wrap gap-2">
                  <v-chip
                    v-for="genre in anime.metadata.genres"
                    :key="genre"
                  >
                    {{ genre }}
                  </v-chip>
                </div>
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
  .v-img__img.v-img__img--cover {
    filter: blur(5px);
    transform: scale(1.05); //remove edge
  }
}
</style>
