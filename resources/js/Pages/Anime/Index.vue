<script lang="ts">

import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  layout: AppLayout,
}
</script>

<script lang="ts" setup>
import { Head, router } from '@inertiajs/vue3'
import {
  mdiAlphabeticalVariant,
  mdiFormatListNumbered,
  mdiPlus,
} from 'mdi-js-es'
import { useDisplay } from 'vuetify'
import { PaginatedResponse } from '@/types'
import InertiaLink from '@/Components/InertiaLink'
import { VBtn } from 'vuetify/components'
import { AnimeData } from '@/types/anime'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
import TableView from '@/Pages/Anime/Partials/TableView.vue'
import { useUserStore } from '@/stores'

import { storeToRefs } from 'pinia'
import { inject, watch } from 'vue'
import AbcView from '@/Pages/Anime/Partials/AbcView.vue'
import { route as ziggyRoute } from 'ziggy-js'

const { mdAndUp } = useDisplay()

const route = inject('route') as typeof ziggyRoute

defineProps<{
  canCreate: boolean
  canViewUnpublished: boolean
  anime: PaginatedResponse<AnimeData>
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
