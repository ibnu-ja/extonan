<script lang="ts" setup>
import { Head, Link as InertiaLink, router, usePage } from '@inertiajs/vue3'
import {
  mdiAlphabeticalVariant,
  mdiFilterMenu,
  mdiFormatListNumbered,
  mdiMagnify,
  mdiPlus,
  mdiSort,
  mdiSortAlphabeticalAscending,
  mdiSortAlphabeticalDescending,
  mdiSortCalendarAscending,
  mdiSortCalendarDescending,
} from 'mdi-js-es'
import { useDisplay } from 'vuetify'
import { PageProps, PaginatedResponse } from '@/types'
import { VBtn, VListItem } from 'vuetify/components'
import { AnimeData, Filter } from '@/types/anime'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'

import { computed, inject, ref } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
import AppLayout from '@/Layouts/AppLayout.vue'
import ThreeStateSelect from '@/Pages/Anime/Partials/ThreeStateSelect.vue'
import { useAnime } from '@/composables/useAniList'
import { debounce } from 'lodash-es'
import Pagination from '@/Components/Pagination.vue'
import AnimeCover from '@/Pages/Anime/Partials/AnimeCover.vue'
import { useLanguages } from '@/composables/useLanguages'

defineOptions({
  name: 'AnimeIndex',
  layout: AppLayout,
})

const { mdAndUp, sm, lgAndUp } = useDisplay()

const route = inject('route') as typeof ziggyRoute

defineProps<{
  canCreate: boolean
  canViewUnpublished: boolean
  anime: PaginatedResponse<AnimeData>
  seasons: string[]
}>()

const filter = route().params.filter as Filter | undefined
const title = ref(filter?.title || null)
const seasonIn = ref<string[]>(filter?.season_in?.split(',') || [] as string[])
const seasonNotIn = ref<string[]>(filter?.season_not_in?.split(',') || [] as string[])

const tagsIn = ref<string[]>(filter?.tag_in?.split(',') || [] as string[])
const tagsNotIn = ref<string[]>(filter?.tag_not_in?.split(',') || [] as string[])

const genresIn = ref<string[]>(filter?.genre_in?.split(',') || [] as string[])
const genresNotIn = ref<string[]>(filter?.genre_not_in?.split(',') || [] as string[])

// console.log('filter', filter)

const searchAction = () => {
  const currentRoute = route().current() || 'anime.index'

  let filter: Filter = {}

  if (seasonIn.value.length > 0) {
    filter.season_in = seasonIn.value.join(',')
  }
  if (seasonNotIn.value.length > 0) {
    filter.season_not_in = seasonNotIn.value.join(',')
  }
  if (tagsIn.value.length > 0) {
    filter.tag_in = tagsIn.value.join(',')
  }
  if (tagsNotIn.value.length > 0) {
    filter.tag_not_in = tagsNotIn.value.join(',')
  }
  if (genresIn.value.length > 0) {
    filter.genre_in = genresIn.value.join(',')
  }
  if (genresNotIn.value.length > 0) {
    filter.genre_not_in = genresNotIn.value.join(',')
  }

  if (title.value)
    filter.title = title.value

  router.visit(route(currentRoute, { filter: filter }), { only: ['anime'], preserveState: true, preserveScroll: true })
}

const { tags, genres } = useAnime()

const showFilter = ref(filter != undefined)

const shortenLength = computed(() => {
  if (sm.value || lgAndUp.value) return 3
  else return 1
})

const SEARCH_DEBOUNCE_TIME = 500

const search = () => {
  const debounced = debounce(searchAction, SEARCH_DEBOUNCE_TIME)
  debounced()
}

const clearSeasons = () => {
  seasonIn.value = []
  seasonNotIn.value = []
  search()
}

const clearTags = () => {
  tagsIn.value = []
  tagsNotIn.value = []
  search()
}

const clearGenres = () => {
  genresIn.value = []
  genresNotIn.value = []
  search()
}

const sorts = [
  {
    name: 'Title',
    value: 'title->romaji',
    icon: mdiSortAlphabeticalAscending,
  },
  {
    name: 'Title',
    value: '-title->romaji',
    icon: mdiSortAlphabeticalDescending,
  },
  {
    name: 'Created',
    value: 'created_at',
    icon: mdiSortCalendarAscending,
  },
  {
    name: 'Created',
    value: '-created_at',
    icon: mdiSortCalendarDescending,
  },
  {
    name: 'Updated',
    value: 'updated_at',
    icon: mdiSortCalendarAscending,
  },
  {
    name: 'Updated',
    value: '-updated_at',
    icon: mdiSortCalendarDescending,
  },
  // {
  //   name: 'Published',
  //   value: 'published_at',
  //   icon:
  // },
  // { name: 'Published',
  //   value: '-published_at',
  //   icon:
  // },
]

const { translate } = useLanguages()

const page = usePage<PageProps>()
</script>

<template>
  <Head title="Anime" />

  <!-- Page Heading -->
  <!-- mt-md-6-->
  <PageHeader title="Anime List">
    <template #append>
      <InertiaLink
        v-if="canCreate"
        :as="VBtn"
        :href="route('anime.create')"
        color="primary"
        :icon="!mdAndUp ? mdiPlus : undefined"
        :prepend-icon="mdAndUp ? mdiPlus : undefined"
        :text="mdAndUp ? 'Create' : undefined"
      />
    </template>
  </PageHeader>

  <v-container :max-width="1800">
    <div class="px-4 md:px-0">
      <div class="searching flex align-center gap-4 mb-4">
        <v-text-field
          v-model="title"
          label="Search"
          variant="outlined"
          hide-details
          clearable
          :prepend-inner-icon="mdiMagnify"
          :append-inner-icon="mdiFilterMenu"
          @click:append-inner="showFilter = !showFilter"
          @update:model-value="search"
        />
        <v-btn-toggle
          :mandatory="true"
        >
          <InertiaLink
            :as="VBtn"
            :href="route('anime.az')"
            value="abc"
            :icon="mdiAlphabeticalVariant"
          />
          <InertiaLink
            active
            :as="VBtn"
            :href="route('anime.index', {...route().params})"
            :icon="mdiFormatListNumbered"
          />
        </v-btn-toggle>

        <v-menu>
          <template #activator="{ props }">
            <v-btn
              rounded="md"
              :icon="mdiSort"
              v-bind="props"
            />
          </template>

          <v-list>
            <InertiaLink
              v-for="item in sorts"
              :key="item.value"
              :as="VListItem"
              :active="route().params.sort === item.value"
              :disabled="route().params.sort === item.value"
              :href="route('anime.index', {...route().params, sort: item.value})"
              :prepend-icon="item.icon"
            >
              <v-list-item-title>{{ item.name }}</v-list-item-title>
            </InertiaLink>
          </v-list>
        </v-menu>
      </div>

      <v-expand-transition>
        <div
          v-show="showFilter"
          class="mb-4"
        >
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <ThreeStateSelect
              v-model="seasonIn"
              v-model:tags-not-in="seasonNotIn"
              :shorten-length
              :items="seasons"
              variant="outlined"
              label="Season"
              clearable
              @click:clear="clearSeasons"
              @update:model-value="search"
            />
            <ThreeStateSelect
              v-model="genresIn"
              v-model:tags-not-in="genresNotIn"
              :shorten-length
              :items="genres"
              variant="outlined"
              label="Genres"
              clearable
              @click:clear="clearGenres"
              @update:model-value="search"
            />
            <ThreeStateSelect
              v-model="tagsIn"
              v-model:tags-not-in="tagsNotIn"
              :shorten-length
              :items="tags"
              variant="outlined"
              label="Tags"
              clearable
              @click:clear="clearTags"
              @update:model-value="search"
            />
          </div>
        </div>
      </v-expand-transition>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4">
      <AnimeCover
        v-for="animeItem in anime.data"
        :key="animeItem.id"
        :is-published="animeItem.is_published"
        :image="animeItem.metadata.coverImage.extraLarge"
        :lazy-img="animeItem.metadata.coverImage.medium"
        :permissions="animeItem.can"
        :title="translate(animeItem.title)"
        :href="animeItem.link"
        :delete-url="route('anime.destroy', animeItem)"
        :edit-url="route('anime.edit', animeItem)"
        :show-action="!!page.props.auth.user"
      />
    </div>
    <Pagination :links="anime.links" />
  </v-container>
</template>
