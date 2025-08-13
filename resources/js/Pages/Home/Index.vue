<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import { AnimeData, EpisodeData } from '@/types/anime'
import LatestAnime from '@/Pages/Home/Partials/LatestAnime.vue'
import dayjs from 'dayjs'
import { CoverImage } from '@/types/anilist'
import { useLanguages } from '@/composables/useLanguages'
import AppLayout from '@/Layouts/AppLayout.vue'
import { MV } from '@/types/mv'
import SingleMvItem from '@/Pages/MV/Partials/SingleMvItem.vue'
import AlbumItem from '@/Pages/Album/Partials/AlbumItem.vue'
import LatestEpisode from '@/Pages/Home/Partials/LatestEpisode.vue'

defineOptions({
  name: 'Home',
  layout: AppLayout,
})

type Postable = EpisodeData & {
  postable: AnimeData
  thumbnail: CoverImage | null
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  metadata: Record<string, any>
}

defineProps<{
  laravelVersion: string
  phpVersion: string
  latestAnime: AnimeData[]
  latestEpisodes: Postable[]
  latestMv: MV[]
  latestAlbum: MV[]
}>()

const { translate } = useLanguages()

</script>

<template>
  <Head title="Home" />
  <section style="position: relative">
    <template v-if="latestAnime.length > 0">
      <LatestAnime :latest-anime />
    </template>
    <v-container v-else>
      Add anime to show latest anime slider.
    </v-container>
  </section>
  <section class="mt-4 sm:mt-8">
    <LatestEpisode
      v-if="latestEpisodes.length > 0"
      :latest-episodes
    />
    <v-container v-else>
      Add anime post to show latest episode data.
    </v-container>
  </section>
  <section class="mt-4 sm:mt-8">
    <template v-if="latestEpisodes.length > 0">
      <v-container
        max-width="1800"
        class="px-2 sm:px-4 pb-0 mb-2 sm:mb-4"
      >
        <h1 class="text-h4 text-md-h3">
          Latest MV
        </h1>
      </v-container>
      <!--TODO swiper-->
      <v-container
        max-width="1800"
        class="px-0 sm:px-4 pt-0 grid grid-cols-1 md:grid-cols-4 sm:grid-cols-2"
      >
        <SingleMvItem
          v-for="mv in latestMv"
          :key="mv.id"
          :post="mv"
        />
      </v-container>
    </template>
    <v-container
      v-else
      max-width="1800"
    >
      Add MV post to show latest MV.
    </v-container>
  </section>
  <section class="mt-4 sm:mt-8">
    <template v-if="latestEpisodes.length > 0">
      <v-container
        max-width="1800"
        class="px-2 sm:px-4 pb-0 mb-2 sm:mb-4"
      >
        <h1 class="text-h4 text-md-h3">
          Latest Album
        </h1>
      </v-container>
      <!--TODO swiper-->
      <v-container
        max-width="1800"
        class="px-0 sm:px-4 pt-0 grid grid-cols-2 md:grid-cols-5 sm:grid-cols-3"
      >
        <AlbumItem
          v-for="album in latestAlbum"
          :key="album.id"
          :show-action="!!$page.props.auth.user"
          :image="album.thumbnail?.extraLarge"
          :lazy-img="album.thumbnail?.medium"
          :href="route('album.show', album)"
          :permissions="album.can"
          :delete-url="route('shinrai.destroy', album)"
          :edit-url="route('album.edit', album)"
          :is-published="album.is_published"
        >
          <template #content>
            <div class="text-subtitle-1 list-title">
              {{ translate(album.title) }}
            </div>
            <div
              class="text-subtitle-2 text-medium-emphasis"
            >
              <template
                v-if="!album.is_published"
              >
                <span
                  class="text-success"
                >
                  Draft
                </span> by
              </template>
              <template v-else>
                {{ dayjs(album.published_at).format('D MMM YYYY') }} &bull;
              </template>
              {{ album.author.name }}
            </div>
          </template>
        </AlbumItem>
      </v-container>
    </template>
    <v-container v-else>
      Add album post to show latest album.
    </v-container>
  </section>
</template>
