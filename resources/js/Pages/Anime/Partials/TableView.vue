<script lang="ts" setup>

import { mdiDelete } from '@mdi/js/commonjs/mdi'
import { mdiPencil } from '@mdi/js'
import dayjs from 'dayjs'
import { PaginatedResponse } from '@/types'
import { router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { AnimeData } from '@/types/anime'
import calendar from 'dayjs/plugin/calendar'
import InertiaLink from '@/Components/InertiaLink.vue'
import { VIcon } from 'vuetify/components'

const page = usePage()

const headers = [
  {
    key: 'id',
    title: 'id',
  },
  {
    key: 'title',
    value: 'title.romaji',
    title: 'Title',
  },
  {
    key: 'created_at',
    title: 'Created Date',
  },
  {
    key: 'updated_at',
    title: 'Last Updated',
  },
  {
    key: 'published_at',
    title: 'Published At',
  },
  {
    key: 'action',
    title: 'Action',
    hide: page.props.auth.user == null,
  },
]

const filteredHeaders = computed(() => {
  return headers.filter(header => !header.hide)
})

dayjs.extend(calendar)

const props = defineProps<{
  canCreate: boolean
  canViewUnpublished: boolean
  anime: PaginatedResponse<AnimeData>
}>()

const perPageChange = (e: number) => {
  router.get(route('anime.index', {
    page: props.anime.current_page,
    perPage: e,
  }), {
    only: ['anime'],
  })
}
type SortItem = { key: string, order?: boolean | 'asc' | 'desc' }

const implodeSort = (sortItems: SortItem[]) => {
  return sortItems.map((sortItem) => {
    let sort
    if (!sortItem.order || sortItem.order === 'desc') {
      sort = '-' + sortItem.key
    } else sort = sortItem.key
    return sort
  }).join(',')
}

const deleting = ref(false)

const selectedItem = ref<AnimeData>()

const showDeletionModal = (item: AnimeData) => {
  selectedItem.value = item
  deleting.value = true
}

const deleteAnime = () => {
  router.delete(route('anime.destroy', selectedItem.value))
  deleting.value = false
}

const expodeSort = (sorts: string): SortItem[] => {
  return sorts.split(',').map((sort) => {
    const isDesc = sort.startsWith('-')
    return {
      key: isDesc ? sort.slice(1) : sort,
      order: isDesc ? 'desc' : 'asc',
    }
  })
}

const currentSort = ref<readonly SortItem[] | undefined>()

if (route().params.sort) {
  currentSort.value = expodeSort(route().params.sort)
}

const sortChange = (e: unknown) => {
  const sort = implodeSort(e as SortItem[])
  router.get(route('anime.index', {
    sort,
  }), {
    only: ['anime'],
  })
}

const pageChange = (e: number) => {
  let url = props.anime.links[e].url?.toString()
  if (url) {
    router.get(url)
  }
}
</script>

<template>
  <v-data-table-server
    :sort-by="currentSort"
    :headers="filteredHeaders"
    :items-length="anime.total"
    :page="anime.current_page"
    :items="anime.data"
    :items-per-page="anime.per_page"
    @update:sort-by="sortChange"
    @update:page="pageChange"
    @update:items-per-page="perPageChange"
  >
    <!--eslint-disable vue/valid-v-slot-->
    <template
      #item.action="{ item }"
    >
      <!--eslint-enable-->
      <div class="d-flex gap-1">
        <v-tooltip
          location="bottom"
          text="Delete"
        >
          <template #activator="{props: propss}">
            <v-icon
              v-bind="propss"
              :disabled="!item.can.delete"
              :icon="mdiDelete"
              color="error"
              @click.prevent="showDeletionModal(item)"
            />
          </template>
        </v-tooltip>

        <v-tooltip
          location="bottom"
          text="Edit"
        >
          <template #activator="{props: propss}">
            <InertiaLink
              :as="VIcon"
              :href="route('anime.edit', item.id)"
              v-bind="propss"
              :disabled="!item.can.update"
              :icon="mdiPencil"
              color="secondary"
            />
          </template>
        </v-tooltip>

        <!--        <v-tooltip-->
        <!--          location="bottom"-->
        <!--          text="Publish"-->
        <!--        >-->
        <!--          <template #activator="{props: propss}">-->
        <!--            <v-icon-->
        <!--              v-bind="propss"-->
        <!--              :disabled="!item.can.publish"-->
        <!--              :icon="mdiSend"-->
        <!--              color="primary"-->
        <!--            />-->
        <!--          </template>-->
        <!--        </v-tooltip>-->
      </div>
    </template>
    <!--eslint-disable vue/valid-v-slot-->
    <template
      #item.created_at="{ item }"
    >
      <span :title="dayjs(item.created_at).toString()">
        {{ item.created_at != null ? dayjs(item.created_at).calendar() : '-' }}
      </span>
      <!--eslint-enable-->
    </template>
    <!--eslint-disable vue/valid-v-slot-->
    <template
      #item.updated_at="{ item }"
    >
      <span :title="dayjs(item.updated_at).toString()">
        {{ item.updated_at != null ? dayjs(item.updated_at).calendar() : '-' }}
      </span>
      <!--eslint-enable-->
    </template>
    <!--eslint-disable vue/valid-v-slot-->
    <template
      #item.published_at="{ item }"
    >
      <span :title="dayjs(item.published_at).toString()">
        {{ item.published_at != null ? dayjs(item.published_at).calendar() : '-' }}
      </span>
      <!--eslint-enable-->
    </template>
  </v-data-table-server>
  <div>
    <v-dialog
      v-model="deleting"
      max-width="600"
    >
      <v-card>
        <v-card-item title="Delete Anime" />
        <v-card-text>
          Are you sure you want to delete this item?
        </v-card-text>
        <v-divider />
        <v-card-actions>
          <v-btn
            @click="deleting = false"
          >
            Cancel
          </v-btn>

          <v-btn
            color="error"
            @click="deleteAnime"
          >
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
