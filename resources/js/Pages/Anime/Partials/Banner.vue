<script lang="ts" setup>

import { CoverImage } from '@/types/anilist'
import { useGradient } from '@/composables/useGradient'
import ActionDropdown from '@/Pages/Anime/Partials/ActionDropdown.vue'
import { Permissions } from '@/types'

const { gradient } = useGradient()

defineProps<{
  permissions?: Permissions
  isPublished: boolean
  deleteUrl?: string
  editUrl?: string
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
                class="mx-16 sm:mx-0"
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
            <v-col class="flex flex-column sm:gap-0 gap-2">
              <v-list-item>
                <v-list-item-title>
                  <h1 class="text-h4 text-md-h3">
                    {{ title }}
                  </h1>
                </v-list-item-title>
                <template #append>
                  <ActionDropdown
                    :edit-url
                    :delete-url
                    :permissions
                    :is-published
                  />
                </template>
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
