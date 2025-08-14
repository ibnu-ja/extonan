<script lang="ts" setup>
import { Head, router } from '@inertiajs/vue3'
import { VBtn } from 'vuetify/components'
import { mdiFormatListNumbered, mdiPlus, mdiViewGrid } from 'mdi-js-es'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
import { PaginatedResponse } from '@/types'
import { inject, watch } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
import { storeToRefs } from 'pinia'
import { useUserStore } from '@/stores'
import { MV } from '@/types/mv'
import TableView from '@/Pages/MV/Partials/TableView.vue'
import dayjs from 'dayjs'
import calendar from 'dayjs/plugin/calendar'
import Pagination from '@/Components/Pagination.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link as InertiaLink } from '@inertiajs/vue3'
import SingleMvItem from '@/Pages/MV/Partials/SingleMvItem.vue'

defineOptions({
  name: 'MVIndex',
  layout: AppLayout,
})

dayjs.extend(calendar)
/*
export type Post = {
  author: UserRelation
  author_id: number
  created_at: Date | string | null
  updated_at: Date | string | null
  published_at: Date | string | null
  uuid: string | null
  is_published: boolean
  is_current: boolean
  publisher?: UserRelation
  publisher_type: string | null
  publisher_id: number | null
  can: Permissions
}
 */
//
// const as = {
//   "id": 1,
//   "description": {"id": null},
//   "slug": {"en": "rezero-starting-life-in-another-world-season-3-bwang"},
//   "title": {"en": "Bwang", "romaji": null},
//   "author_id": 1,
//   "metadata": {"post_type": "tv", "ep_no": "1"},
//   "postable_type": "App\\Models\\Anime",
//   "postable_id": 1,
//   "created_at": "2024-11-13T15:15:42.000000Z",
//   "updated_at": "2024-11-13T15:15:42.000000Z",
//   "uuid": "9c712446-1ed1-4725-b228-21ffa5ecb1ac",
//   "published_at": "2024-11-13T15:15:42.000000Z",
//   "is_published": true,
//   "is_current": true,
//   "publisher_type": "App\\Models\\User",
//   "publisher_id": 1,
//   "thumbnail": null,
//   "can": {"update": true, "publish": true, "delete": true},
//   "media": []
// }

defineProps<{
  canCreate: boolean
  canViewUnpublished: boolean
  mv: PaginatedResponse<MV>
}>()

const route = inject('route') as typeof ziggyRoute

const { displayModeMV } = storeToRefs(useUserStore())

watch(displayModeMV, (value) => {
  if (value != 'list') {
    router.visit(route('mv.index', {
      perPage: 15,
    }))
  }
})

</script>
<template>
  <InertiaLink
    position="fixed"
    class="m-[12px]"
    location="bottom right"
    style="z-index: 1005"
    :href="route('mv.create')"
    :as="VBtn"
    size="large"
    color="primary"
    :icon="true"
  >
    <v-icon :icon="mdiPlus" />
  </InertiaLink>

  <Head title="Music Video" />
  <PageHeader title="MV List">
    <!--<template #append>-->
    <!--  <InertiaLink-->
    <!--    v-if="canCreate"-->
    <!--    :as="VBtn"-->
    <!--    :href="route('mv.create')"-->
    <!--    color="primary"-->
    <!--    :icon="!mdAndUp ? mdiPlus : undefined"-->
    <!--    :prepend-icon="mdAndUp ? mdiPlus : undefined"-->
    <!--    :text="mdAndUp ? 'Create' : undefined"-->
    <!--  />-->
    <!--</template>-->
  </PageHeader>

  <v-container class="my-0">
    <v-btn-toggle
      v-model="displayModeMV"
      :mandatory="true"
    >
      <v-btn
        value="grid"
        :icon="mdiViewGrid"
      />
      <v-btn
        value="list"
        :icon="mdiFormatListNumbered"
      />
    </v-btn-toggle>
  </v-container>
  <v-container class="px-0">
    <v-tabs-window v-model="displayModeMV">
      <v-tabs-window-item value="grid">
        <!--TODO grid view-->
        <template
          v-if="mv.data.length > 0"
        >
          <div class="grid md:grid-cols-2 lg:grid-cols-3">
            <SingleMvItem
              v-for="post in mv.data"
              :key="post.id"
              :post="post"
            />
          </div>
          <Pagination :links="mv.links" />
        </template>
        <p v-else>
          No mv.
        </p>
        <!--{{ mv.data }}-->
        <!--<TableView-->
        <!--  :can-view-unpublished-->
        <!--  :can-create-->
        <!--  :mv-->
        <!--/>-->
      </v-tabs-window-item>
      <v-tabs-window-item value="list">
        <TableView
          :can-view-unpublished
          :can-create
          :mv
        />
      </v-tabs-window-item>
    </v-tabs-window>
  </v-container>
</template>
