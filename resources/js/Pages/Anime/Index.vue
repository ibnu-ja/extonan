<script lang="ts" setup>
import { Head, router } from '@inertiajs/vue3'
import { mdiAlphabeticalVariant, mdiFormatListNumbered, mdiPlus } from 'mdi-js-es'
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
import TagSelection from '@/Pages/Anime/Partials/TagSelection.vue'

defineOptions({
  name: 'AnimeIndex',
  layout: AppLayout,
})

const { mdAndUp } = useDisplay()

const route = inject('route') as typeof ziggyRoute

const props = defineProps<{
  canCreate: boolean
  canViewUnpublished: boolean
  anime: PaginatedResponse<AnimeData>
  seasons: string[]
}>()

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

const seasonIn = ref<string[]>([])
const seasonNotIn = ref<string[]>([])

const clickFilterSeason = (season: string) => {
  console.log(season)
  if (!seasonIn.value.includes(season) && !seasonNotIn.value.includes(season)) {
    console.log('tambahkan')
    seasonIn.value.push(season)
  } else if (seasonIn.value.includes(season) && !seasonNotIn.value.includes(season)) {
    console.log('not in')
    seasonIn.value = seasonIn.value.filter(seasonItem => seasonItem != season)
    seasonNotIn.value.push(season)
  } else if (!seasonIn.value.includes(season) && seasonNotIn.value.includes(season)) {
    console.log('hapus')
    seasonNotIn.value = seasonNotIn.value.filter(seasonItem => seasonItem != season)
  }
}

const displaySeason = computed(() => {
  return props.seasons.map((season) => {
    let color
    if (seasonIn.value.includes(season)) {
      color = 'success'
    } else if (seasonNotIn.value.includes(season)) {
      color = 'error'
    }

    return {
      name: season,
      color: color,
    }
  })
})

const tagsIn = ref<string[]>([])
const tagsNotIn = ref<string[]>([])
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

  <v-container class="my-0">
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
  </v-container>

  <!--  </template>-->
  <v-container class="px-0">
    <div>
      filter[season_in]: {{ seasonIn.join(',') }} <span
        v-show="seasonIn.length > 0"
        class="text-success"
      >+{{
        seasonIn.length
      }}</span>
    </div>
    <div>
      filter[season_not_in]: {{ seasonNotIn.join(',') }} <span
        v-show="seasonNotIn.length > 0"
        class="text-error"
      >-{{ seasonNotIn.length }}</span>
    </div>
    <div class="flex gap-2 my-2">
      <v-chip
        v-for="season in displaySeason"
        :key="season.name"
        :color="season.color"
        @click="clickFilterSeason(season.name)"
      >
        {{ season.name }}
      </v-chip>
    </div>
    <div>
      tagsIn: {{ tagsIn }}
    </div>
    <div>
      tagsNotIn: {{ tagsNotIn }}
    </div>
    <TagSelection
      v-model="tagsIn"
      v-model:tags-not-in="tagsNotIn"
    />
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
