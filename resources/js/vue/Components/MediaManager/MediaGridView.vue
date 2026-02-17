<script lang="ts" setup>
import { mdiImage, mdiOpenInNew } from 'mdi-js-es'
import { computed } from 'vue'

import { imageLink } from '@/composables/useImageLink'
import { Media } from '@/types'
import prettyBytes from 'pretty-bytes'

const props = defineProps<{
  images: Media | Media[]
}>()

const imageLists = computed(() => {
  if (props.images instanceof Array) {
    return props.images
  } else {
    return [props.images]
  }
})

</script>

<template>
  <v-menu
    v-for="item in imageLists"
    :key="item.id"
    location="end"
  >
    <template #activator="{props: props2}">
      <v-card
        ripple
        v-bind="props2"
      >
        <v-img
          aspect-ratio="1"
          cover
          :src="imageLink(item)?.medium"
        >
          <template #placeholder>
            <v-row
              class="fill-height m-0"
              align="center"
              justify="center"
            >
              <v-progress-circular
                indeterminate
                color="grey-lighten-5"
              />
            </v-row>
          </template>
        </v-img>
      </v-card>
    </template>

    <v-list
      density="compact"
      width="400px"
    >
      <v-list-item>
        <strong class="text-bold">Filename: </strong> {{ `${item.filename}.${item.extension}` }}
      </v-list-item>
      <v-list-item><strong class="text-bold">Size: </strong> {{ prettyBytes(item.size) }}</v-list-item>
      <v-list-item @click="() => {}">
        <v-dialog
          close-on-content-click
          fullscreen
          activator="parent"
        >
          <v-img
            height="100%"
            :src="imageLink(item)?.original"
            :lazy-src="imageLink(item)?.medium"
          />
        </v-dialog>
        <v-list-item-title>
          View image
        </v-list-item-title>
        <template #append>
          <v-icon :icon="mdiImage" />
        </template>
      </v-list-item>
      <v-list-item
        :href="imageLink(item)?.original"
        title="Open in New Tab"
        :target="`media-${item.id}`"
        :append-icon="mdiOpenInNew"
      />
    </v-list>
  </v-menu>
</template>
