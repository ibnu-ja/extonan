<script lang="ts" setup>

import { mdiDelete, mdiPencil } from 'mdi-js-es'
import dayjs from 'dayjs'
import { PaginatedResponse } from '@/types'
import { router, usePage } from '@inertiajs/vue3'
import { computed, inject, ref } from 'vue'
import calendar from 'dayjs/plugin/calendar'
import InertiaLink from '@/Components/InertiaLink'
import { VIcon } from 'vuetify/components'
import { route as ziggyRoute } from 'ziggy-js'
import { useLanguages } from '@/composables/useLanguages'
import { MV } from '@/types/mv'

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

const route = inject('route') as typeof ziggyRoute

const props = defineProps<{
  canCreate: boolean
  canViewUnpublished: boolean
  mv: PaginatedResponse<MV>
}>()

const perPageChange = (e: number) => {
  router.get(route('mv.index', {
    page: props.mv.current_page,
    perPage: e,
  }), {
    only: ['mv'],
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

const selectedItem = ref<MV>()

const showDeletionModal = (item: MV) => {
  selectedItem.value = item
  deleting.value = true
}

const deleteMV = () => {
  router.delete(route('mv.destroy', selectedItem.value))
  deleting.value = false
}

const explodeSort = (sorts: string): SortItem[] => {
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
  currentSort.value = explodeSort(route().params.sort)
}

const sortChange = (e: unknown) => {
  const sort = implodeSort(e as SortItem[])
  router.get(route('mv.index', {
    sort,
  }), {
    only: ['mv'],
  })
}

const pageChange = (e: number) => {
  let url = props.mv.links[e].url?.toString()
  if (url) {
    router.get(url)
  }
}

const { translate } = useLanguages()
</script>

<template>
  <v-data-table-server
    :sort-by="currentSort"
    :headers="filteredHeaders"
    :items-length="mv.total"
    :page="mv.current_page"
    :items="mv.data"
    :items-per-page="mv.per_page"
    @update:sort-by="sortChange"
    @update:page="pageChange"
    @update:items-per-page="perPageChange"
  >
    <!--eslint-disable vue/valid-v-slot-->
    <template
      #item.action="{ item } : { item: MV }"
    >
      <div class="flex gap-1">
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
              :href="route('mv.edit', item.id)"
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
    <template
      #item.title="{ item } : { item: MV }"
    >
      <InertiaLink
        :href="route('mv.show', item.id)"
        class="no-underline hover:underline"
      >
        {{ translate(item.title) }}
      </InertiaLink>
      <v-chip
        v-if="!item.is_published"
        class="ml-2"
        color="success"
      >
        Draft
      </v-chip>
    </template>
    <template
      #item.created_at="{ item } : { item: MV }"
    >
      <span :title="dayjs(item.created_at).toString()">
        {{ item.created_at != null ? dayjs(item.created_at).calendar() : '-' }}
      </span>
    </template>
    <template
      #item.updated_at="{ item } : { item: MV }"
    >
      <span :title="dayjs(item.updated_at).toString()">
        {{ item.updated_at != null ? dayjs(item.updated_at).calendar() : '-' }}
      </span>
    </template>
    <template
      #item.published_at="{ item } : { item: MV }"
    >
      <span :title="dayjs(item.published_at).toString()">
        {{ item.published_at != null ? dayjs(item.published_at).calendar() : '-' }}
      </span>
    </template>
    <!--eslint-enable-->
  </v-data-table-server>
  <div>
    <v-dialog
      v-model="deleting"
      max-width="600"
    >
      <v-card>
        <v-card-item title="Delete MV" />
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
            @click="deleteMV"
          >
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
