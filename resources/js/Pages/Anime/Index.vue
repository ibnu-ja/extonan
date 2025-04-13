<script lang="ts" setup>
import { Head, router } from '@inertiajs/vue3'
import { mdiAlphabeticalVariant, mdiFilterMenu, mdiFormatListNumbered, mdiMagnify, mdiPlus } from 'mdi-js-es'
import { useDisplay } from 'vuetify'
import { PaginatedResponse } from '@/types'
import InertiaLink from '@/Components/InertiaLink'
import { VBtn } from 'vuetify/components'
import { AnimeData } from '@/types/anime'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
import TableView from '@/Pages/Anime/Partials/TableView.vue'
import { useUserStore } from '@/stores'

import { storeToRefs } from 'pinia'
import { computed, inject, ref, watch } from 'vue'
import AbcView from '@/Pages/Anime/Partials/AbcView.vue'
import { route as ziggyRoute } from 'ziggy-js'
import AppLayout from '@/Layouts/AppLayout.vue'
import ThreeStateSelect from '@/Pages/Anime/Partials/ThreeStateSelect.vue'
import { useAnime } from '@/composables/useAniList'
import { debounce } from 'lodash-es'

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

type Filter = {
  title?: string
  season_in?: string
  season_not_in?: string
  tag_in?: string
  tag_not_in?: string
  genre_in?: string
  genre_not_in?: string
}

const { displayMode } = storeToRefs(useUserStore())

watch(displayMode, (value) => {
  if (value != 'abc') {
    return
  }
  if (route().params.perPage == '-1') {
    return
  }
  router.visit(route('anime.index', {
    perPage: -1,
  }))
})

const filter = route().params.filter as Filter | undefined
const title = ref(filter?.title || null)
const seasonIn = ref<string[]>(filter?.season_in?.split(',') || [] as string[])
const seasonNotIn = ref<string[]>(filter?.season_not_in?.split(',') || [] as string[])

const tagsIn = ref<string[]>(filter?.tag_in?.split(',') || [] as string[])
const tagsNotIn = ref<string[]>(filter?.tag_not_in?.split(',') || [] as string[])

const genresIn = ref<string[]>(filter?.genre_in?.split(',') || [] as string[])
const genresNotIn = ref<string[]>(filter?.genre_not_in?.split(',') || [] as string[])

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

  <v-container class="px-0">
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
        <!--<v-btn-->
        <!--  size="large"-->
        <!--  :prepend-icon="mdiFilterMenu"-->
        <!--  text="Filter"-->
        <!--/>-->

        <v-btn-toggle
          v-model="displayMode"
          :mandatory="true"
        >
          <v-btn
            value="abc"
            :icon="mdiAlphabeticalVariant"
          />
          <v-btn
            value="list"
            :icon="mdiFormatListNumbered"
          />
          <!--      <v-btn-->
          <!--        value="tile"-->
          <!--        :icon="mdiViewGrid"-->
          <!--      />-->
        </v-btn-toggle>
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

      <!--<div class="flex align-center gap-4">-->
      <!--  <v-spacer />-->
      <!--  <v-btn-->
      <!--    color="primary"-->
      <!--    @click="search"-->
      <!--  >-->
      <!--    Search-->
      <!--  </v-btn>-->

      <!--  <InertiaLink-->
      <!--    :as="VBtn"-->
      <!--    color="secondary"-->
      <!--    :href="route('anime.index')"-->
      <!--    :only="['anime']"-->
      <!--  >-->
      <!--    Reset Search-->
      <!--  </InertiaLink>-->
      <!--</div>-->
    </div>
    TODO genre
    <v-tabs-window v-model="displayMode">
      <v-tabs-window-item value="abc">
        <AbcView
          :can-view-unpublished
          :can-create
          :anime
        />
      </v-tabs-window-item>
      <v-tabs-window-item value="list">
        <TableView
          :can-view-unpublished
          :can-create
          :anime
        />
      </v-tabs-window-item>
      <!--      <v-tabs-window-item value="tile">-->
      <!--        gridView-->
      <!--      </v-tabs-window-item>-->
    </v-tabs-window>
  </v-container>
</template>
