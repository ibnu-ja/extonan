<script lang="ts" setup>

import { useDisplay } from 'vuetify'
import { VCard } from 'vuetify/components'
import { mdiPlay } from '@mdi/js'
import InertiaLink from '@/Components/InertiaLink.vue'
import ItemListTitle from '@/Pages/Anime/Partials/ItemListTitle.vue'
import { Permissions } from '@/types'
import { MaybeElement, useElementSize } from '@vueuse/core'
import { ref } from 'vue'

defineProps<{
  href?: string
  permissions?: Permissions
  title: string
  subtitle?: string
  overhead?: string
  image?: string
  lazyImg?: string
  editUrl?: string
  deleteUrl?: string
  isPublished: boolean
}>()

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
        :height="(width / 16) * 9"
        cover
        rounded="lg"
        :src="image"
        :lazy-src="lazyImg"
      >
        <v-fade-transition>
          <div
            v-if="isHovering"
            class="!flex transition-fast-in-fast-out bg-grey-darken-4 v-card--reveal"
            style="height: 100%;"
          >
            <v-icon :icon="mdiPlay" />
          </div>
        </v-fade-transition>
      </v-img>

      <ItemListTitle
        class="mt-2"
        :permissions
        :overhead
        :title
        :subtitle
        :edit-url
        :delete-url
        :is-published
      />
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
