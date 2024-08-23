<script lang="ts">

import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  layout: AppLayout,
}
</script>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { inject, ref } from 'vue'
import { useDisplay } from 'vuetify'
import { mdiClose, mdiContentSave, mdiSend } from '@mdi/js'
import dayjs from 'dayjs'

import objectSupport from 'dayjs/plugin/objectSupport'
import { mdiDelete } from '@mdi/js/commonjs/mdi'
import Metadata from '@/Pages/Anime/Partials/Metadata.vue'
import Casts from '@/Pages/Anime/Partials/Casts.vue'
import { useAnime } from '@/composables/useAniList'
import { AnimeMediaAutofillResponse } from '@/types/anilist'
import { TranslatableField } from '@/types/formHelper'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
import { route as ziggyRoute } from 'ziggy-js'
import { useLanguages } from '@/composables/useLanguages'

dayjs.extend(objectSupport)

const props = defineProps<{
  canPublish: boolean
  anime: AnimeForm & { id: number } | null
}>()

const route = inject('route') as typeof ziggyRoute

const { selectedLanguage: currentLang, languages } = useLanguages()

type AnimeForm = {
  title: TranslatableField
  description: TranslatableField
  anilist_id: number | null
  is_published: boolean
  metadata: unknown
}

const form = useForm<AnimeForm>({
  title: {
    en: null,
    id: null,
    native: null,
    romaji: null,
  },
  is_published: true,
  description: {
    en: null,
    id: null,
  },
  anilist_id: null,
  metadata: null,
},
)
const apiType = ref<'MAL' | 'Anilist'>('MAL')
const apiSearchId = ref<number | null>(null)
const anilistData = ref<AnimeMediaAutofillResponse>()

const { smAndUp, mdAndUp } = useDisplay()

const { animeApi } = useAnime()

const fetchAnilistData = async () => {
  if (!apiSearchId.value) {
    console.error('id is not set')
    return
  }
  try {
    const response = await animeApi(apiSearchId.value, apiType.value === 'MAL')
    anilistData.value = response
    form.title.en = response?.title.english
    form.title.romaji = response?.title.romaji
    form.title.native = response?.title.native
    form.description.en = response?.description
    form.anilist_id = response?.id ?? null
    form.metadata = response
  } catch (e) {
    console.error(e)
  }
}

const clearAnilist = () => anilistData.value = undefined

const submit = () => {
  if (props.anime) {
    console.log('update')
    form.put(route('anime.update', props.anime?.id))
  } else {
    console.log('saveNew')
    form.post(route('anime.store'))
  }
}

const save = () => {
  if (props.anime?.is_published) {
    form.is_published = props.anime.is_published
  }
  submit()
}

if (props.anime != null) {
  apiSearchId.value = props.anime.anilist_id!
  fetchAnilistData()
  form.title = props.anime.title
  form.is_published = props.anime.is_published
  form.description = props.anime.description
  form.anilist_id = props.anime.anilist_id
}

const title = props.anime?.title ? 'Editing ' + props.anime?.title.en : 'Create Anime'

</script>

<template>
  <Head :title />
  <PageHeader :title>
    <template #append>
      <div class="flex gap-2">
        <v-btn
          variant="outlined"
          color="error"
          :icon="mdAndUp ? mdiDelete : undefined"
          :prepend-icon="mdAndUp ? mdiDelete : undefined"
          :text="mdAndUp ? 'Delete' : undefined"
        />
        <v-btn
          :variant="form.is_published ? undefined : 'outlined'"
          :type="canPublish && !form.is_published? undefined : 'submit'"
          form="storeAnime"
          :disabled="form.processing"
          :color=" form.is_published ? 'primary' : 'secondary'"
          :icon="mdAndUp ? mdiContentSave : undefined"
          :prepend-icon="mdAndUp ? mdiContentSave : undefined"
          :text="mdAndUp ? 'Save' : undefined"
          @click.prevent="save"
        />
        <v-btn
          v-if="canPublish && !form.is_published"
          form="storeAnime"
          type="submit"
          :disabled="form.processing"
          color="primary"
          :icon="mdAndUp ? mdiSend : undefined"
          :prepend-icon="mdAndUp ? mdiSend : undefined"
          :text="mdAndUp ? 'Publish' : undefined"
        />
      </div>
    </template>
  </PageHeader>
  <v-container class="p-0 sm:p-4">
    <v-form
      id="storeAnime"
      :disabled="form.processing"
      @submit.prevent="submit"
    >
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
                  :error-messages="form.errors.title"
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
                  :error-messages="form.errors.description"
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
                <v-card-title>
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
                  :error-messages="form.errors.anilist_id! || form.errors.metadata!"
                  placeholder="placeholder"
                  variant="outlined"
                  hide-details="auto"
                  @keydown.enter.prevent="fetchAnilistData"
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
    </v-form>
  </v-container>
</template>
