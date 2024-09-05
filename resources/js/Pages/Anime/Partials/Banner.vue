<script lang="ts" setup>

import { CoverImage } from '@/types/anilist'
import { useGradient } from '@/composables/useGradient'
import { Permissions } from '@/types'
import { TranslatableField } from '@/types/formHelper'
import { VCard } from 'vuetify/components'

const { gradient } = useGradient()

defineProps<{
  permissions?: Permissions
  isPublished: boolean
  deleteUrl?: string
  editUrl?: string
  bg: string
  coverImage: CoverImage
  title: TranslatableField
  genres: string[]
  description: TranslatableField
}>()
</script>

<template>
  <v-card
    :rounded="false"
    class="flex relative elevation-0 border-0"
    :elevation="0"
    max-height="440"
    variant="text"
  >
    <template #image>
      <v-img
        :gradient
        class="bg-blur"
        cover
        :src="coverImage.medium"
      />
    </template>
    <v-container>
      <div class="sm:p-4 gap-4 flex sm:items-start flex-col sm:flex-row">
        <v-img
          style="cursor: pointer;"
          cover
          rounded
          width="200"
          class="flex-0-0 self-center sm:self-start"
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

        <div>
          <h3 class="text-h4 mb-2">
            {{ title.en }}
          </h3>
          <h3 class="text-h5 mb-2 text-medium-emphasis">
            {{ title.native }}
          </h3>
          <h3 class="text-h5 mb-2 text-medium-emphasis">
            {{ title.romaji }}
          </h3>
          <div class="gap-2 mb-4 hidden sm:flex flex-wrap">
            <v-chip
              v-for="genre in genres"
              :key="genre"
            >
              {{ genre }}
            </v-chip>
          </div>
          <p class="text-medium-emphasis text-subtitle-1 hidden sm:block">
            {{ description.en }}
          </p>
        </div>
      </div>
    </v-container>
  </v-card>
  <v-container class="py-0">
    <div class="gap-2 mb-4 sm:hidden flex flex-wrap">
      <v-chip
        v-for="genre in genres"
        :key="genre"
      >
        {{ genre }}
      </v-chip>
    </div>
    <p class="text-medium-emphasis text-subtitle-1 sm:hidden block">
      {{ description.en }}
    </p>
  </v-container>
</template>

<style lang="scss">
.bg-blur {
  .v-img__img.v-img__img--cover {
    filter: blur(5px);
    transform: scale(1.05); //remove edge
  }
}
</style>
