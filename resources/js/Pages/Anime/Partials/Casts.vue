<script setup lang="ts">
import { Anime } from '@/types/anilist'
import { useDisplay } from 'vuetify'

defineProps<{
  data: Anime
}>()

const { smAndUp } = useDisplay()
</script>

<template>
  <div
    v-if="data && data.characters?.edges"
    class="mb-6"
  >
    <v-card-title>Casts</v-card-title>
    <div class="d-grid grid-cos-1 grid-cols-md-2 gap-4 text-body-2">
      <v-card
        v-for="character in data.characters.edges"
        :key="character.node.id"
        :rounded="smAndUp"
      >
        <div
          class="d-grid grid-cols-2"
        >
          <div class="d-grid grid-cols-4">
            <v-img
              :src="character.node.image.large"
              :lazy-src="character.node.image.medium"
            />

            <div class="px-3 py-1 col-span-3 d-flex flex-column justify-space-between">
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
              class="d-grid grid-cols-4"
            >
              <div class="px-3 py-1 col-span-3 text-right d-flex flex-column justify-space-between">
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
