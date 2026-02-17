<script setup lang="ts">

import AppLayout from '@/Layouts/AppLayout.vue'
import { PageProps, PaginatedResponse } from '@/types'
import { MV } from '@/types/mv'
import Pagination from '@/Components/Pagination.vue'
import { useLanguages } from '@/composables/useLanguages'
import { VBtn } from 'vuetify/components'
import { mdiPlus } from 'mdi-js-es'
import { Head, usePage } from '@inertiajs/vue3'
import { Link as InertiaLink } from '@inertiajs/vue3'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
import dayjs from 'dayjs'
import AlbumItem from '@/Pages/Album/Partials/AlbumItem.vue'

defineOptions({
  name: 'AlbumIndex',
  layout: AppLayout,
})

defineProps<{
  albums: PaginatedResponse<MV>
}>()

const { translate } = useLanguages()

const page = usePage<PageProps>()
</script>

<template>
  <InertiaLink
    position="fixed"
    class="m-[12px]"
    location="bottom right"
    style="z-index: 1005"
    :href="route('album.create')"
    :as="VBtn"
    size="large"
    color="primary"
    :icon="true"
  >
    <v-icon :icon="mdiPlus" />
  </InertiaLink>

  <Head title="Album" />
  <PageHeader title="Album List" />
  <v-container>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
      <AlbumItem
        v-for="post in albums.data"
        :key="post.id"
        :show-action="!page.props.auth.user"
        :image="post.thumbnail?.extraLarge"
        :lazy-img="post.thumbnail?.medium"
        :href="route('album.show', post)"
        :permissions="post.can"
        :delete-url="route('shinrai.destroy', post)"
        :edit-url="route('album.edit', post)"
        :is-published="post.is_published"
      >
        <template #content>
          <div class="text-subtitle-1 list-title">
            {{ translate(post.title) }}
          </div>
          <div
            class="text-subtitle-2 text-medium-emphasis"
          >
            <template
              v-if="!post.is_published"
            >
              <span
                class="text-success"
              >
                Draft
              </span> by
            </template>
            <template v-else>
              {{ dayjs(post.published_at).format('D MMM YYYY') }} &bull;
            </template>
            {{ post.author?.name }}
          </div>
        </template>
      </AlbumItem>
    </div>
    <Pagination :links="albums.links" />
  </v-container>
</template>
