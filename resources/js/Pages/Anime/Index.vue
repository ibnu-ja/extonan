<script lang="ts">

import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  layout: AppLayout,
}
</script>

<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import {
  mdiAlphabeticalVariant,
  mdiFormatListNumbered,
  mdiPlus,
} from '@mdi/js'
import { useDisplay } from 'vuetify'
import { PaginatedResponse } from '@/types'
import { ref } from 'vue'
import InertiaLink from '@/Components/InertiaLink.vue'
import { VBtn } from 'vuetify/components'
import { AnimeData } from '@/types/anime'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
import TableView from '@/Pages/Anime/Partials/TableView.vue'

const { mdAndUp } = useDisplay()

defineProps<{
  canCreate: boolean
  canViewUnpublished: boolean
  anime: PaginatedResponse<AnimeData>
}>()

const tab = ref('abc')
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
      v-model="tab"
      mandatory
    >
      <v-btn
        value="abc"
        :icon="mdiAlphabeticalVariant"
      />
      <v-btn
        value="list"
        :icon="mdiFormatListNumbered"
      />
    </v-btn-toggle>
  </v-container>

  <!--  </template>-->
  <v-container class="px-0">
    <TableView
      :can-view-unpublished
      :can-create
      :anime
    />
  </v-container>
</template>
