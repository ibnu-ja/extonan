<script setup lang="ts">
import { AnimeMediaAutofillResponse } from '@/types/anilist'
import { useDisplay } from 'vuetify'

type Props = {
  data: AnimeMediaAutofillResponse
  showTitle?: boolean
}

withDefaults(defineProps<Props>(), {
  showTitle: true,
})

const { smAndUp } = useDisplay()
</script>

<template>
  <div
    v-if="data && data.characters?.edges"
    class="mb-6"
  >
    <v-card-title
      v-if="showTitle"
    >
      Casts
    </v-card-title>
    <div class="grid grid-cos-1 md:grid-cols-2 gap-4 text-body-2">
      <v-card
        v-for="character in data.characters.edges"
        :key="character.node.id"
        :rounded="smAndUp"
      >
        <div
          class="grid grid-cols-2"
        >
          <div class="grid grid-cols-4">
            <v-img
              :src="character.node.image.large"
              :lazy-src="character.node.image.medium"
            />

            <div class="px-3 py-1 col-span-3 flex flex-column justify-space-between">
              <div>
                {{ character.node.name.full }}
              </div>
              <div class="text-caption font-weight-light">
                {{ character.role }}
              </div>
            </div>
          </div>
          <v-slide-x-transition>
            <div
              v-if="character.voiceActors[0]?.name"
              class="grid grid-cols-4"
            >
              <div class="px-3 py-1 col-span-3 text-right flex flex-column justify-space-between">
                <div>
                  {{ character.voiceActors[0].name.full }}
                </div>
                <div class="font-weight-light">
                  Japanese
                </div>
              </div>
              <v-img
                :src="character.voiceActors[0].image.large"
                :lazy-src="character.voiceActors[0].image.medium"
              />
            </div>
          </v-slide-x-transition>
        </div>
      </v-card>
    </div>
  </div>
</template>

<style scoped>

</style>
