<script lang="ts" setup>
import { Head, router } from '@inertiajs/vue3'
import Layout from '@/Layouts/AppLayout.vue'
import { mdiPlus } from '@mdi/js'
import { useDisplay } from 'vuetify'
import { PaginatedResponse } from '@/types'
import { TranslatableField } from '@/types/formHelper'
import { ref } from 'vue'
import InertiaLink from '@/Components/InertiaLink.vue'
import { VBtn } from 'vuetify/components'

const { mdAndUp } = useDisplay()
type AnimeData = {
  id: number
  anilist_id: number
  description: TranslatableField
  slug: string
  title: TranslatableField
  author_id: number
  created_at: string
  updated_at: string
  uuid: string
  published_at: string
  is_published: boolean
  is_current: boolean
  publisher_type: string
  publisher_id: number
}

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
]

const props = defineProps<{
  canCreate: boolean
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
    console.log(sortItem)
    let sort
    if (!sortItem.order || sortItem.order === 'desc') {
      sort = '-' + sortItem.key
    } else sort = sortItem.key
    return sort
  }).join(',')
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

const currentSort = ref<SortItem[] | null>([])

if (route().params.sort) {
  currentSort.value = expodeSort(route().params.sort)
}

const sortChange = (e: unknown) => {
  console.log(e)
  const sort = implodeSort(e as SortItem[])
  console.log(sort)
  router.get(route('anime.index', {
    sort,
  }), {
    only: ['anime'],
  })
}

const pageChange = (e: number) => {
  router.get(
    props.anime.links[e].url,
  )
}

</script>

<template>
  <Head title="Anime" />
  <Layout>
    <template #header>
      <div class="d-flex justify-space-between align-end">
        <h1 class="text-h4 text-md-h3">
          Anime List
        </h1>

        <InertiaLink
          v-if="canCreate"
          :as="VBtn"
          :href="route('anime.create')"
          color="primary"
          :icon="!mdAndUp ? mdiPlus : undefined"
          :prepend-icon="mdAndUp ? mdiPlus : undefined"
          :text="mdAndUp ? 'Create' : undefined"
        />
      </div>
    </template>
    <v-container>
      <v-data-table-server
        :sort-by="currentSort"
        :headers="headers"
        :items-per-page-options="[1,5,10]"
        :items-length="anime.total"
        :page="anime.current_page"
        :items="anime.data"
        :items-per-page="anime.per_page"
        @update:sort-by="sortChange"
        @update:page="pageChange"
        @update:items-per-page="perPageChange"
      />
      <!--      -->
    </v-container>
  </Layout>
</template>
