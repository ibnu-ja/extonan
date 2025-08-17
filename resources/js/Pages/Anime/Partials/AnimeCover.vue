<script setup lang="ts">

import { VCard } from 'vuetify/components'
import ItemListTitle from '@/Pages/Anime/Partials/ItemListTitle.vue'
import { Permissions } from '@/types'
import { useDisplay } from 'vuetify/framework'

withDefaults(defineProps<{
  href?: string
  permissions?: Permissions
  title?: string
  subtitle?: string
  overhead?: string | null
  image?: string
  lazyImg?: string
  editUrl?: string
  deleteUrl?: string
  isPublished: boolean
  showAction?: boolean
}>(), {
  showAction: false,
  subtitle: undefined,
  overhead: undefined,
  image: undefined,
  lazyImg: undefined,
  editUrl: undefined,
  deleteUrl: undefined,
  href: undefined,
  permissions: undefined,
  title: undefined,
})

const { smAndUp } = useDisplay()

import { Link as InertiaLink } from '@inertiajs/vue3'

</script>

<template>
  <InertiaLink
    :href
    :as="VCard"
    variant="text"
    class="p-2 flex flex-col"
    :rounded="smAndUp ? 'lg' : false"
  >
    <v-img
      ref="imageRef"
      color="surface"
      rounded="lg"
      cover
      :src="image"
      :lazy-src="lazyImg"
      aspect-ratio="3/4"
    />

    <ItemListTitle
      :show-action
      class="mt-2 flex-1"
      :permissions
      :overhead
      :title
      :subtitle
      :edit-url
      :delete-url
      :is-published
    >
      <template
        v-if="$slots.content"
        #content
      >
        <slot name="content" />
      </template>
    </ItemListTitle>
  </InertiaLink>
</template>
