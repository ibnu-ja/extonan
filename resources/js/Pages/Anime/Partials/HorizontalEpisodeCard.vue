<script lang="ts" setup>

import { useDisplay } from 'vuetify'
import { VCard } from 'vuetify/components'
import { mdiPlay } from 'mdi-js-es'
import { Link as InertiaLink } from '@inertiajs/vue3'
import ItemListTitle from '@/Pages/Anime/Partials/ItemListTitle.vue'
import { Permissions } from '@/types'
import { MaybeElement, useElementSize } from '@vueuse/core'
import { ref } from 'vue'

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

const imageRef = ref<MaybeElement>()

const { width } = useElementSize(imageRef)

</script>

<template>
  <v-hover v-slot="{isHovering, props: propsss}">
    <InertiaLink
      v-bind="propsss"
      :href
      :as="VCard"
      variant="text"
      class="p-2"
      :rounded="smAndUp ? 'lg' : false"
    >
      <v-img
        ref="imageRef"
        color="surface"
        :height="(width / 16) * 9"
        cover
        rounded="lg"
        :src="image"
        :lazy-src="lazyImg"
      >
        <v-fade-transition>
          <div
            v-if="isHovering || !image"
            class="!flex transition-fast-in-fast-out bg-grey-darken-4 v-card--reveal"
            style="height: 100%;"
          >
            <v-icon :icon="mdiPlay" />
          </div>
        </v-fade-transition>
      </v-img>

      <ItemListTitle
        :show-action
        class="mt-2"
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
  </v-hover>
</template>

<style scoped>
.v-card--reveal {
  align-items: center;
  bottom: 0;
  justify-content: center;
  opacity: .5;
  position: absolute;
  width: 100%;
}
</style>
