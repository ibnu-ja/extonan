<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3'
import { computed, inject, ref } from 'vue'
import { useDisplay } from 'vuetify'
import { mdiClose, mdiContentSave, mdiDelete, mdiSend } from 'mdi-js-es'
import dayjs from 'dayjs'

import objectSupport from 'dayjs/plugin/objectSupport'
import Metadata from '@/Pages/Anime/Partials/Metadata.vue'
import Casts from '@/Pages/Anime/Partials/Casts.vue'
import { useAnime } from '@/composables/useAniList'
import { AnimeMediaAutofillResponse } from '@/types/anilist'
import { TranslatableField } from '@/types/formHelper'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
import { route as ziggyRoute } from 'ziggy-js'
import { useLanguages } from '@/composables/useLanguages'
import { openConfirmationDialog } from '@/composables/useDialog'
import AppLayout from '@/Layouts/AppLayout.vue'

dayjs.extend(objectSupport)

defineOptions({
  name: 'AnimeCreate',
  layout: AppLayout,
})

const props = defineProps<{
  canPublish: boolean
  anime?: AnimeForm & { id: number }
}>()

const route = inject('route') as typeof ziggyRoute

const { selectedLanguage: currentLang, languages } = useLanguages()

type AnimeForm = {
  title: TranslatableField
  description: TranslatableField
  anilist_id: number | null
  is_published: boolean
  metadata: AnimeMediaAutofillResponse | null
}

// @ts-ignore
const form = useForm<AnimeForm>({
  title: {
    en: null,
    id: null,
    native: null,
    romaji: null,
  },
  is_published: props.anime?.is_published ?? false,
  description: {
    en: null,
    id: null,
  },
  anilist_id: null,
  metadata: null,
})

const { translate } = useLanguages()
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
    form.metadata = response!
  } catch (e) {
    console.error(e)
  }
}

const clearAnilist = () => {
  anilistData.value = undefined
  form.metadata = null
}

const submit = () => {
  if (props.anime) {
    form.is_published = props.anime.is_published
    form.put(route('anime.update', props.anime?.id))
  } else {
    form.post(route('anime.store'))
  }
}

const save = () => {
  submit()
}

const publish = () => {
  form.is_published = true
  submit()
}

if (props.anime) {
  apiSearchId.value = props.anime.metadata!.idMal
  fetchAnilistData()
  form.title = props.anime.title
  form.description = props.anime.description
  form.anilist_id = props.anime.anilist_id
  form.metadata = props.anime.metadata
}

const title = props.anime?.title ? 'Editing ' + translate(props.anime?.title) : 'Create Anime'

const deletePost = async () => {
  try {
    const confirmed = await openConfirmationDialog('Are you sure want to delete this item?')
    if (confirmed && props.anime?.id) {
      router.delete(route('anime.destroy', props.anime.id))
    }
  } catch {
    //
  }
}
// eslint-disable-next-line @typescript-eslint/no-explicit-any
const formErrors = computed(() => form.errors as any)
</script>

<template>
  <Head :title />
  <PageHeader :title>
    <template #append>
      <div class="flex gap-2">
        <v-btn
          variant="outlined"
          color="error"
          :icon="!mdAndUp ? mdiDelete : undefined"
          :prepend-icon="mdAndUp ? mdiDelete : undefined"
          :text="mdAndUp ? 'Delete' : undefined"
          @click="deletePost"
        />
        <v-btn
          :variant="anime?.is_published ? undefined : 'outlined'"
          :type="canPublish && !form.is_published? undefined : 'submit'"
          form="storeAnime"
          :disabled="form.processing"
          :color="anime?.is_published ? 'primary' : 'secondary'"
          :icon="!mdAndUp ? mdiContentSave : undefined"
          :prepend-icon="mdAndUp ? mdiContentSave : undefined"
          :text="mdAndUp ? 'Save' : undefined"
          @click.prevent="save"
        />
        <v-btn
          v-if="canPublish && !anime?.is_published"
          :disabled="form.processing"
          color="primary"
          :icon="!mdAndUp ? mdiSend : undefined"
          :prepend-icon="mdAndUp ? mdiSend : undefined"
          :text="mdAndUp ? 'Publish' : undefined"
          @click.prevent="publish"
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
      <div class="grid gap-4 grid-cols-1 md:grid-cols-12">
        <div class="md:col-span-8">
          <section>
            <v-card :rounded="smAndUp">
              <v-card-item title="Basic Information" />
              <v-divider />
              <v-card-text>
                <v-text-field
                  v-model="form.title[currentLang.value]"
                  :label="`Title (${currentLang.value})`"
                  :error-messages="formErrors['title' + currentLang]"
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
                <v-text-field
                  v-model="form.title.romaji"
                  label="Title Romaji"
                  :error-messages="formErrors['title.romaji']"
                  variant="outlined"
                  hide-details="auto"
                  class="mb-4"
                />
                <v-text-field
                  v-model="form.title.native"
                  label="Title Native"
                  :error-messages="formErrors['title.native'] as string"
                  variant="outlined"
                  hide-details="auto"
                  class="mb-4"
                />
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
        </div>
        <div class="md:col-span-4">
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
                  type="numeric"
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
                    v-if="form.metadata"
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
                <div
                  v-if="anilistData"
                >
                  <v-divider />
                  <v-card-text>
                    <Metadata
                      :data="anilistData"
                    />
                  </v-card-text>
                </div>
              </v-expand-transition>
            </v-card>
          </div>
        </div>
      </div>
    </v-form>
  </v-container>
</template>
