<script lang="ts" setup>
import { AnimeData } from '@/types/anime'

import { register } from 'swiper/element/bundle'
import { useGradient } from '@/composables/useGradient'
import { VBtn, VCard, VChip } from 'vuetify/components'
import { useLanguages } from '@/composables/useLanguages'
import { mdiApi, mdiCircle, mdiPlay } from 'mdi-js-es'
import { computed, onMounted, ref } from 'vue'
import { useDisplay } from 'vuetify'
import InertiaLink from '@/Components/InertiaLink'
import emblaCarouselVue from 'embla-carousel-vue'
import HorizontalEpisodeCard from '@/Pages/Anime/Partials/HorizontalEpisodeCard.vue'
import dayjs from 'dayjs'
import { usePage } from '@inertiajs/vue3'
import { PageProps } from '@/types'

// register()

const props = defineProps<{
  latestAnime: AnimeData[]
}>()

const { gradient } = useGradient()
//
// const { mdAndUp } = useDisplay()
//
// const { translate } = useLanguages()
//
// const [emblaRef] = emblaCarouselVue({ loop: false, slidesToScroll: 1, containScroll: 'trimSnaps', align: 'start' }, [])
//
// const slide = ref(0)
//
// const pressButton = (value: number) => {
//   console.log(value)
//   swiper.value?.swiper.slideTo(value)
// }
//
// const anime = computed(() => props.latestAnime[0])

const page = usePage<PageProps>()

const { translate } = useLanguages()

const [emblaRef, emblaApi] = emblaCarouselVue({ loop: false, slidesToScroll: 1, align: 'start' }, [])

const selected = ref<number>(0)

onMounted(() => {
  emblaApi.value?.on('select', () => {
    selected.value = emblaApi.value!.selectedScrollSnap()
  })
})

</script>
<template>
  <v-container
    max-width="1800"
    class="px-2 sm:px-4 pb-0 mb-2 sm:mb-4 relative"
  >
    <!-- Slider -->
    <div
      ref="emblaRef"
      class="embla overflow-hidden h-full"
    >
      <div class="flex h-full">
        <div
          v-for="(anime) in latestAnime"
          :key="anime.id"
          class="embla__slide flex-none basis-[100%] min-w-0 h-[350px]"
        >
          <VCard
            class="h-full bg-cover bg-center"
            :style="{ backgroundImage: `linear-gradient(${gradient}), url('${anime.metadata.bannerImage || anime.metadata.coverImage.extraLarge}')`, backgroundSize: 'cover' }"
            rounded="lg"
          >
            <div class="p-10 flex gap-2 h-full min-h-0">
              <div class="basis-1/2 flex flex-col justify-end">
                <h3 class="text-h2 mb-4">
                  {{ translate(anime.title) }}
                </h3>
                <!--<p-->
                <!--  class="line-clamp-2 mb-4"-->
                <!--  :title="translate(anime.description)"-->
                <!--&gt;-->
                <!--  {{ translate(anime.description) }}-->
                <!--</p>-->
                <div class="px-4 sm:px-0 flex gap-2 mb-4 flex-wrap">
                  <InertiaLink
                    v-for="genre in anime.metadata.genres"
                    :key="genre"
                    :as="VChip"
                    :href="route('anime.index', { filter: {genre_in: genre} })"
                  >
                    {{ genre }}
                  </InertiaLink>
                </div>
              </div>
              <!-- 3:4 Image Fallback -->
              <div class="basis-1/2 flex min-h-0">
                <!--<div-->
                <!--  v-if="!anime.metadata.bannerImage"-->
                <!--  class="h-full aspect-[3/4] overflow-hidden rounded-lg"-->
                <!--&gt;-->
                <!--  <img-->
                <!--    :src="anime.metadata.coverImage.extraLarge"-->
                <!--    alt=""-->
                <!--    class="h-full w-full object-cover object-center"-->
                <!--  >-->
                <!--</div>-->
              </div>
            </div>
          </VCard>
        </div>
      </div>
    </div>

    <!-- Slider Selector Overlay -->
    <div
      v-if="latestAnime.length > 1"
      class="absolute bottom-5 right-10 z-10"
    >
      <v-item-group
        v-model="selected"
        mandatory
      >
        <v-item
          v-for="(anime, n) in latestAnime"
          :key="anime.id"
          v-slot="{ isSelected }"
        >
          <v-btn
            :active="isSelected"
            size="x-small"
            variant="text"
            class="v-carousel__controls__item swiper-pagination mr-0"
            :icon="mdiCircle"
            @click="() => {
              emblaApi?.scrollTo(n)
            }"
          />
        </v-item>
      </v-item-group>
    </div>
  </v-container>
</template>

<style lang="scss">
.bg-blur {
  .v-img__img.v-img__img--cover {
    filter: blur(5px);
    transform: scale(1.05); //remove edge
  }
}
</style>
