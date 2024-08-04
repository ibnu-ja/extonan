<script setup lang="ts">

import Layout from '@/Layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { useDisplay } from 'vuetify'
import { mdiClose, mdiContentSave, mdiSend } from '@mdi/js'
import dayjs from 'dayjs'

import objectSupport from 'dayjs/plugin/objectSupport'
import { mdiDelete } from '@mdi/js/commonjs/mdi'
import Metadata from '@/Pages/Anime/Partials/Metadata.vue'
import Casts from '@/Pages/Anime/Partials/Casts.vue'
import { useAnime } from '@/composables/useAniList'
import { Anime } from '@/types/anilist'
import { LanguageItem, TranslatableField } from '@/types/formHelper'

dayjs.extend(objectSupport)

const languages: LanguageItem[] = [
  {
    label: 'English',
    value: 'en',
  }, {
    label: 'Indonesia',
    value: 'id',
  }, {
    label: 'Native',
    value: 'native',
  }, {
    label: 'Romaji',
    value: 'romaji',
  },
]

type AnimeForm = {
  title: TranslatableField
  description: TranslatableField
  anilist_id: number | null
  is_published: boolean
}

const form = useForm<AnimeForm>({
  title: {
    en: null,
    id: null,
    native: null,
    romaji: null,
  },
  is_published: false,
  description: {
    en: null,
    id: null,
  },
  anilist_id: null,
},
)
const apiType = ref<'MAL' | 'Anilist'>('MAL')
const apiSearchId = ref<number | null>(null)
const anilistData = ref<Anime>()

const { smAndUp, mdAndUp } = useDisplay()
const currentLang = ref<LanguageItem>(languages[0])

const { animeApi } = useAnime()

const fetchAnilistData = async () => {
  if (!apiSearchId.value) {
    console.error('id is not set')
    return
  }

  anilistData.value = await animeApi(apiSearchId.value, apiType.value === 'MAL')
}

const clearAnilist = () => anilistData.value = undefined

</script>

<template>
  <Head title="Create Anime" />
  <Layout>
    <template #header>
      <div class="d-flex justify-space-between align-end">
        <h1 class="text-h4 text-md-h3">
          Create Anime
        </h1>
        <div class="d-flex gap-2">
          <v-btn
            variant="outlined"
            color="error"
            :icon="!mdAndUp ? mdiDelete : undefined"
            :prepend-icon="mdAndUp ? mdiDelete : undefined"
            :text="mdAndUp ? 'Delete' : undefined"
          />
          <v-btn
            variant="outlined"
            color="secondary"
            :icon="!mdAndUp ? mdiContentSave : undefined"
            :prepend-icon="mdAndUp ? mdiContentSave : undefined"
            :text="mdAndUp ? 'Save' : undefined"
          />
          <v-btn
            color="primary"
            :icon="!mdAndUp ? mdiSend : undefined"
            :prepend-icon="mdAndUp ? mdiSend : undefined"
            :text="mdAndUp ? 'Publish' : undefined"
          />
        </div>
      </div>
    </template>
    <v-container class="pa-0 pa-sm-4">
      <v-row>
        <v-col
          cols="12"
          md="8"
        >
          <section class="mb-4">
            <v-card :rounded="smAndUp">
              <v-card-item title="Basic Information" />
              <v-divider />
              <v-card-text>
                <v-text-field
                  v-model="form.title[currentLang.value]"
                  :label="`Title (${currentLang.value})`"
                  variant="outlined"
                  hide-details="auto"
                  class="mb-4"
                >
                  <template
                    #prepend
                  >
                    <v-select
                      v-model="currentLang"
                      :multiple="false"
                      label="Title language"
                      return-object
                      variant="outlined"
                      :items="languages"
                      item-title="label"
                      hide-details
                      item-value="value"
                    />
                  </template>
                </v-text-field>
                <v-textarea
                  v-model="form.description[currentLang.value]"
                  :label="`Description (${currentLang.value})`"
                  variant="outlined"
                  hide-details="auto"
                  class="mb-4"
                />
              </v-card-text>
            </v-card>
          </section>

          <v-expand-transition>
            <Casts
              v-if="anilistData"
              :data="anilistData"
            />
          </v-expand-transition>
        </v-col>
        <v-col
          cols="12"
          md="4"
        >
          <div>
            <v-expand-x-transition>
              <v-card
                v-if="anilistData && anilistData.coverImage"
                :rounded="smAndUp"
                class="mb-4"
              >
                <v-img
                  :src="anilistData.coverImage.extraLarge"
                  :lazy-src="anilistData.coverImage.medium"
                />
              </v-card>
            </v-expand-x-transition>
            <v-card :rounded="smAndUp">
              <v-card-item>
                <v-card-title class="d-flex">
                  Metadata
                </v-card-title>
              </v-card-item>
              <v-divider />
              <v-card-text>
                <!-- template -->
                <v-select
                  v-model="apiType"
                  label="Api Type"
                  variant="outlined"
                  hide-details
                  class="mb-4"
                  :items="['MAL', 'Anilist']"
                />
                <v-text-field
                  v-model="apiSearchId"
                  :label="apiType + ' ID'"
                  type="number"
                  placeholder="placeholder"
                  variant="outlined"
                  hide-details="auto"
                />
              </v-card-text>
              <v-divider />
              <v-card-actions>
                <v-expand-x-transition>
                  <v-btn
                    :append-icon="mdiClose"
                    text="Clear"
                    @click="clearAnilist"
                  />
                </v-expand-x-transition>
                <v-spacer />
                <v-btn
                  text="Autofill"
                  @click="fetchAnilistData"
                />
              </v-card-actions>

              <v-expand-transition>
                <Metadata
                  v-if="anilistData"
                  :data="anilistData"
                />
              </v-expand-transition>
            </v-card>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </Layout>
</template>
