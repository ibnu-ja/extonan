<script lang="ts" setup>
import ItemListTitle from '@/Pages/Anime/Partials/ItemListTitle.vue'
import { useDisplay } from 'vuetify'
import { mdiPlay } from '@mdi/js'
import InertiaLink from '@/Components/InertiaLink'
import { VCard } from 'vuetify/components'
import { Permissions } from '@/types'

const propss = withDefaults(
  defineProps<{
    // episode: EpisodeData
    href?: string
    title: string
    subtitle?: string
    overhead?: string
    image?: string
    lazyImg?: string
    active?: boolean
    id?: string
    class?: string
    permissions?: Permissions
    isPublished?: boolean
    deleteUrl?: string
    editUrl?: string
    noThumbnail?: boolean
    showAction?: boolean
  }>(),
  {
    noThumbnail: false,
    href: undefined,
    subtitle: undefined,
    overhead: undefined,
    image: undefined,
    lazyImg: undefined,
    active: undefined,
    id: undefined,
    class: undefined,
    permissions: undefined,
    isPublished: false,
    deleteUrl: undefined,
    editUrl: undefined,
    showAction: false,
  },
)

const { smAndUp } = useDisplay()
</script>

<template>
  <v-hover v-slot="{isHovering, props}">
    <InertiaLink
      :id
      :class="propss.class"
      :color="active ? 'primary' : undefined"
      :href
      :as="VCard"
      :rounded="smAndUp ? 'lg' : false"
      class="flex gap-2 p-2"
      :variant="active ? 'tonal' : 'text'"
      v-bind="props"
    >
      <v-img
        v-if="!noThumbnail"
        color="surface"
        width="150"
        class="rounded align-self-center flex-0-0 cursor-pointer"
        :src="image"
        :lazy-src="lazyImg"
        :aspect-ratio="16/9"
        cover
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
      <div
        class="col-span-4"
        style="flex: 1; line-clamp: 2"
      >
        <ItemListTitle
          :show-action
          :permissions
          :edit-url
          :delete-url
          :is-published
          :subtitle
          :title
        />
      </div>
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
