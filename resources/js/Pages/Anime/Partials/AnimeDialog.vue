<script setup lang="ts">

import { VCard } from 'vuetify/components'
import { AnimeMediaAutofillResponse } from '@/types/anilist'
import { TranslatableField } from '@/types/formHelper'
import { BaseLangage } from '@/types/anime'
import { useLanguages } from '@/composables/useLanguages'
import { UserRelation } from '@/types'
import { mdiClose } from 'mdi-js-es'
import AnimeMetadata from '@/Pages/Anime/Partials/AnimeMetadata.vue'
import { computed } from 'vue'

const { hasTranslation } = useLanguages()

type Props = {

  metadata: AnimeMediaAutofillResponse
  title: TranslatableField<BaseLangage>
  description: TranslatableField<BaseLangage>
  airingDate: string
  // eslint-disable-next-line vue/prop-name-casing
  created_at: Date | string | null
  // eslint-disable-next-line vue/prop-name-casing
  updated_at: Date | string | null
  // eslint-disable-next-line vue/prop-name-casing
  published_at: Date | string | null
  // eslint-disable-next-line vue/prop-name-casing
  is_published: boolean
  author: UserRelation
  publisher: UserRelation
}

const props = defineProps<Props>()
const alternativeTitles = computed(() => {
  return hasTranslation(props.title, false)
    ? [
        props.title.native!,
        props.title.romaji!,
      ]
    : [props.title.native!]
})

const model = defineModel<boolean>()

const { translate } = useLanguages()
</script>

<template>
  <v-dialog
    v-model="model"
    fullscreen
  >
    <v-card>
      <v-card-item>
        <template #title>
          <VCardTitle>
            {{ translate(title) }}
          </VCardTitle>
        </template>
        <template #append>
          <v-btn
            :icon="mdiClose"
            density="comfortable"
            variant="text"
            @click.prevent="model = false"
          />
        </template>
      </v-card-item>
      <v-card-text>
        <div class="mb-4">
          {{ translate(description) }}
        </div>
        <AnimeMetadata
          :alternative-titles="alternativeTitles"
          :metadata
          :airing-date
          :created_at
          :updated_at
          :published_at
          :is_published
          :author
          :publisher
        />
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
