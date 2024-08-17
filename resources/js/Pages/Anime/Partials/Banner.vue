<script lang="ts" setup>

import { usePreferredColorScheme } from '@vueuse/core'
import { computed } from 'vue'
import { CoverImage } from '@/types/anilist'

const preferredColorScheme = usePreferredColorScheme()

const gradient = computed(() => {
  if (preferredColorScheme.value === 'dark') {
    return '0deg, rgb(18,18,18) 10%, rgba(18,18,18,0.7) 70%, rgba(18,18,18,0.3) 100%'
  }
  return '0deg, rgba(238,238,238,1) 10%, rgba(238,238,238,0.7) 70%, rgba(238,238,238,0.2) 100%'
})

defineProps<{
  bg: string
  coverImage: CoverImage
  title: string
}>()
</script>

<template>
  <v-card
    :rounded="0"
    variant="text"
    class="mb-4"
  >
    <div
      class="bg"
      :style="{
        backgroundImage: `linear-gradient(${gradient}), url(${bg})`,
      }"
    >
      <div
        style="backdrop-filter: blur(5px);"
      >
        <v-container class="pb-0">
          <v-row
            no-gutters
            class="gap-4"
          >
            <v-col
              sm="3"
              md="2"
              cols="12"
            >
              <v-img
                style="cursor: pointer;"
                cover
                class="mx-16 mx-sm-0"
                :src="coverImage.extraLarge"
                :lazy-src="coverImage.medium"
              >
                <v-dialog
                  close-on-content-click
                  fullscreen
                  activator="parent"
                >
                  <v-img
                    height="100%"
                    :src="coverImage.extraLarge"
                    :lazy-src="coverImage.medium"
                  />
                </v-dialog>
              </v-img>
            </v-col>
            <v-col class="d-flex flex-column gap-sm-0 gap-2">
              <v-list-item>
                <v-list-item-title>
                  <h1 class="text-h4 text-md-h3">
                    {{ title }}
                  </h1>
                </v-list-item-title>
              </v-list-item>
              <slot name="description" />
            </v-col>
          </v-row>
        </v-container>
      </div>
      <!--      -->
    </div>
  </v-card>
</template>

<style>
.bg {
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center center;
}
</style>
