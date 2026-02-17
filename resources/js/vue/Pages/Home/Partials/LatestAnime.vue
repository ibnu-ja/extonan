<script lang="ts" setup>
import { AnimeData } from '@/types/anime'

import { useGradient } from '@/composables/useGradient'
import { useLanguages } from '@/composables/useLanguages'
import { mdiCircle } from 'mdi-js-es'
import { onMounted, ref } from 'vue'
import { Link as InertiaLink } from '@inertiajs/vue3'
import emblaCarouselVue from 'embla-carousel-vue'
import { VCard, VChip } from 'vuetify/components'
import Autoplay from 'embla-carousel-autoplay'

// register()

defineProps<{
  latestAnime: AnimeData[]
}>()

const { gradient } = useGradient()

const { translate } = useLanguages()

const [emblaRef, emblaApi] = emblaCarouselVue({ loop: true, slidesToScroll: 1, align: 'start' }, [Autoplay()])

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
          class="embla__slide flex-none basis-[100%] min-w-0 h-[250px] md:h-[350px] mr-4 last:mr-0 cursor-pointer select-none"
        >
          <InertiaLink
            v-ripple="false"
            :href="route('anime.show', anime)"
            :as="VCard"
            class="h-full bg-cover bg-center"
            :style="{ backgroundImage: `linear-gradient(${gradient}), url('${anime.metadata.bannerImage || anime.metadata.coverImage.extraLarge}')`, backgroundSize: 'cover' }"
            rounded="lg"
          >
            <div class="p-5 md:p-10 flex gap-2 h-full min-h-0">
              <div
                class="
                w-full
                flex flex-col
                justify-start items-center
                md:justify-end md:items-start
              "
              >
                <h3 class="text-h4 text-md-h3 mb-4">
                  {{ translate(anime.title) }}
                </h3>
                <!--<p-->
                <!--  class="line-clamp-2 mb-4"-->
                <!--  :title="translate(anime.description)"-->
                <!--&gt;-->
                <!--  {{ translate(anime.description) }}-->
                <!--</p>-->
                <div
                  class="
                  gap-2 mb-4 flex flex-wrap w-full
                  justify-center items-center
                  md:justify-start md:items-start
                "
                >
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
            </div>
          </InertiaLink>
        </div>
      </div>
    </div>

    <!-- Slider Selector Overlay -->
    <div
      v-if="latestAnime.length > 1"
      class="absolute bottom-2 left-1/2 -translate-x-1/2 md:bottom-5 md:right-10 md:left-auto md:translate-x-0 whitespace-nowrap z-10"
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
            class="v-carousel__controls__item swiper-pagination mr-0 cursor-pointer"
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
