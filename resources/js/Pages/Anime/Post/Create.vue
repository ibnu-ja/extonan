<script lang="ts">

import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  layout: AppLayout,
}
</script>

<script lang="ts" setup>

import { AnimeData, Resource } from '@/types/anime'
import { Head, useForm } from '@inertiajs/vue3'
import { mdiDelete } from '@mdi/js/commonjs/mdi'
import { mdiContentSave, mdiPlus, mdiSend } from '@mdi/js'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
import { useDisplay } from 'vuetify'
import { TranslatableField } from '@/types/formHelper'
import { useLanguages } from '@/composables/useLanguages'
import Metadata from '@/Pages/Anime/Partials/Metadata.vue'
import { inject } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
import { openFormDialog } from '@/composables/useDialog'
const props = defineProps<{
  anime: AnimeData
  canPublish: boolean
}>()
const route = inject('route') as typeof ziggyRoute

const { mdAndUp } = useDisplay()

type PostForm = {
  title: TranslatableField
  description: TranslatableField
  is_published: boolean
  metadata: unknown
  resources: Resource[]
}

const form = useForm<PostForm>({
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
  metadata: null,
  resources: [],
},
)

const submit = () => {
  // if (props.anime) {
  //   console.log('update')
  //   form.put(route('post.update', props.anime, props))
  // } else {
  //   console.log('saveNew')
  form.post(route('post.store', props.anime))
  // }
}
const save = () => {
  // if (props.anime?.is_published) {
  //   form.is_published = props.anime.is_published
  // }
  submit()
}

const { smAndUp } = useDisplay()

const { languages, selectedLanguage: currentLang } = useLanguages()

const addFilename = async () => {
  const result = await openFormDialog('Tambahkan File')
  form.resources.push({
    name: result,
    value: [{
      name: '',
      value: '',
    }],
    type: 'link',
  })
}

const addLink = (index) => {
  form.resources[index].value.push({
    name: '',
    value: '',
  })
}

const editFilename = async (index) => {
  form.resources[index].name = await openFormDialog('Edit filename', undefined, {
    label: 'Filename',
    variant: 'outlined',
    placeholder: form.resources[index].name,
  })
}
</script>

<template>
  <Head title="Add Episode" />
  <PageHeader title="Add Episode">
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
          form="storePost"
          :disabled="form.processing"
          :color=" form.is_published ? 'primary' : 'secondary'"
          :icon="mdAndUp ? mdiContentSave : undefined"
          :prepend-icon="mdAndUp ? mdiContentSave : undefined"
          :text="mdAndUp ? 'Save' : undefined"
          @click.prevent="save"
        />
        <v-btn
          v-if="canPublish && !form.is_published"
          form="storePost"
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
  <v-container class="p-0 p-sm-4">
    <v-form
      id="storePost"
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
          <section class="mb-4">
            <v-card :rounded="smAndUp">
              <v-card-item>
                <v-card-title>
                  Links
                </v-card-title>
                <template #append>
                  <v-btn
                    color="primary"
                    :prepend-icon="mdiPlus"
                    @click.prevent="addFilename"
                  >
                    Add
                  </v-btn>
                </template>
              </v-card-item>
              <v-divider />
              <v-card-text class="flex gap-4 flex-col">
                <v-card
                  v-for="(resource, i) in form.resources"
                  :key="i"
                  variant="outlined"
                >
                  <v-card-item>
                    <v-card-title>
                      {{ resource.name }}
                    </v-card-title>
                    <template #append>
                      <div class="flex flex-wrap gap-2">
                        <v-btn
                          color="secondary"
                          @click="editFilename(i)"
                        >
                          Edit
                        </v-btn>
                        <v-btn color="error">
                          Delete
                        </v-btn>
                        <v-btn
                          color="primary"
                          @click.prevent="addLink(i)"
                        >
                          Add
                        </v-btn>
                      </div>
                    </template>
                  </v-card-item>
                  <template
                    v-for="(links, j) in resource.value"
                    :key="j"
                  >
                    <v-card-text>
                      <v-row no-gutters>
                        <v-col cols="3">
                          <div class="text-medium-emphasis mt-2">
                            Name
                          </div>
                        </v-col>
                        <v-col cols="9">
                          <v-text-field
                            v-model="form.resources[i].value[j].name"
                            placeholder="Gudang"
                            variant="outlined"
                            hide-details="auto"
                            class="mb-4"
                          />
                        </v-col>
                      </v-row>
                      <v-row no-gutters>
                        <v-col cols="3">
                          <div class="text-medium-emphasis mt-2">
                            Link/Embed code
                          </div>
                        </v-col>
                        <v-col cols="9">
                          <v-textarea
                            v-model="form.resources[i].value[j].value"
                            placeholder="https://gudang.extonan.id/"
                            rows="2"
                            variant="outlined"
                            hide-details="auto"
                          />

                          <v-btn color="error mt-4">
                            Delete
                          </v-btn>
                        </v-col>
                      </v-row>
                    </v-card-text>
                    <v-divider v-if="j < resource.value.length" />
                  </template>
                </v-card>
              <!--  -->
              </v-card-text>
            </v-card>
          </section>
        </v-col>
        <v-col
          cols="12"
          md="4"
        >
          <div>
            <!--TODO-->
            todo: input gambar thumbnail
            <v-expand-x-transition>
              <v-card
                :rounded="smAndUp"
                class="mb-4"
              >
                <v-img
                  :src="anime.metadata.coverImage.extraLarge"
                  :lazy-src="anime.metadata.coverImage.medium"
                />
              </v-card>
            </v-expand-x-transition>
            <v-card :rounded="smAndUp">
              <v-card-item>
                <v-card-title>
                  Anime
                </v-card-title>
              </v-card-item>
              <v-divider />
              <v-card-text>
                <!-- template -->
                <div>
                  <v-list-subheader>
                    Title
                  </v-list-subheader>
                  {{ anime.title.en }}
                </div>
                <div>
                  <v-list-subheader>
                    Links
                  </v-list-subheader>
                  <div class="flex gap-1">
                    <v-chip
                      prepend-avatar="https://upload.wikimedia.org/wikipedia/commons/6/61/AniList_logo.svg"
                      :href="`https://anilist.co/anime/${anime.anilist_id}`"
                      target="_blank"
                    >
                      Anilist
                    </v-chip>
                    <v-chip
                      prepend-avatar="https://upload.wikimedia.org/wikipedia/commons/9/9b/MyAnimeList_favicon.svg"
                      :href="`https://myanimelist.net/anime/${anime.anilist_id}`"
                      target="_blank"
                    >
                      MyAnimelist
                    </v-chip>
                  </div>
                </div>
              </v-card-text>

              <v-expand-transition>
                <Metadata
                  v-if="anime.metadata"
                  :data="anime.metadata"
                />
              </v-expand-transition>
            </v-card>
          </div>
        </v-col>
      </v-row>
    </v-form>
  </v-container>
</template>
