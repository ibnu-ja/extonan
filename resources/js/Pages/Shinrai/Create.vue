<script lang="ts" setup>

import { Head, useForm } from '@inertiajs/vue3'
import { mdiClose, mdiContentSave, mdiDelete, mdiPencil, mdiPlus, mdiSend } from 'mdi-js-es'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
import { useDisplay } from 'vuetify'
import { useLanguages } from '@/composables/useLanguages'
import { computed, inject, ref } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
import { openFormDialog } from '@/composables/useDialog'
import MediaManager from '@/Components/MediaManager/Index.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { MV, MVPostItem } from '@/types/mv'
import axios from 'axios'
import { Album, Disc } from '@/types/vgmdb'

defineOptions({
  name: 'ShinraiCreate',
  layout: AppLayout,
})

const props = defineProps<{
  post?: MV
  canPublish: boolean
}>()

const route = inject('route') as typeof ziggyRoute
const { mdAndUp } = useDisplay()
const form = useForm<MVPostItem>({
  description: {
    en: null,
    id: null,
  },
  is_published: true,
  links: [],
  metadata: { post_type: route().params.type || 'mv', vgmdb_data: null },
  thumbnail_item: null,
  title: {
    en: null,
    id: null,
    native: null,
    romaji: null,
  },
})

const vgmdbUrl = import.meta.env.VITE_VGMDB_API_URL as string

// assign props to form model
if (props.post) {
  form.title = props.post.title
  form.description = props.post.description
  form.links = props.post.links
  form.metadata = props.post.metadata
  form.thumbnail_item = props.post.thumbnail_item
}

const apiSearchId = ref<number | null>(null)

const fetchVgmdbData = async () => {
  if (!apiSearchId.value) {
    console.error('id is not set')
    return
  }
  try {
    const response = await axios.get<Album>(`${vgmdbUrl}/album/${apiSearchId.value}`)
    form.metadata.vgmdb_data = response.data
    console.log(form.metadata.vgmdb_data)
    form.title.en = response.data.names.en
    form.title.native = response.data.names.ja
    form.title.romaji = response.data.names['ja-latn']
    if (response.data.discs[0].tracks[0].names)
      trackLangs.value = Object.keys(response.data.discs[0].tracks[0].names)
  } catch (e) {
    console.error(e)
  }
}
const submit = () => {
  if (props.post) {
    form.is_published = props.post.is_published
    form.put(route('shinrai.update', props.post))
  } else {
    form.post(route('shinrai.store'))
  }
}
const save = () => {
  form.is_published = false
  submit()
}

const { smAndUp } = useDisplay()

const { languages, selectedLanguage: currentLang } = useLanguages()

const addFilename = async () => {
  try {
    const result = await openFormDialog('Tambahkan File')
    form.links.push({
      name: result,
      value: [{
        name: '',
        value: '',
      }],
      type: 'link',
    })
  } catch {
    //
  }
}

const addLink = (index: number) => {
  form.links[index].value.push({
    name: '',
    value: '',
  })
}

const editFilename = async (index: number) => {
  try {
    form.links[index].name = await openFormDialog('Edit filename', undefined, {
      label: 'Filename',
      variant: 'outlined',
      placeholder: form.links[index].name,
    })
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
  } catch (e) { /* empty */
  }
}

const deleteFilename = (index: number) => {
  form.links.splice(index, 1)
}

const deleteLink = (i: number, j: number) => {
  form.links[i].value.splice(j, 1)
}
const trackLangs = ref<string[]>([])

// const title = props.post ? 'Editing ' + translate(props.post?.title) : 'Create Shinrai Post'
const title = 'Create Shinrai Post'

const deletePost = async () => {
  alert('delete')
  // try {
  //   const confirmed = await openConfirmationDialog('Are you sure want to delete this item?')
  //   if (confirmed && props.post?.id && props.anime.id) {
  //     router.delete(route('post.destroy', [props.anime.id, props.post.id]))
  //   }
  // } catch {
  //   //
  // }
}

function addTrack(disc: Disc) {
  disc!.tracks!.push({
    names: {},
    track_length: '',
  })
}

// TODO translations
const postType = [
  {
    value: 'mv',
    title: 'Music Video',
  },
  {
    value: 'album',
    title: 'album',
  },
  {
    value: 'single',
    title: 'Single',
  },
]

function deleteTrackLanguage(index: number) {
  const langName = trackLangs.value[index] as string
  form.metadata.vgmdb_data?.discs?.forEach((disc) => {
    disc.tracks?.forEach((track) => {
      if (track.names && track.names[langName])
        delete track.names[langName]
      else console.log('not track lang found')
    })
  })
  trackLangs.value.splice(index, 1)
}

const addDisc = () => {
  alert('a')
  form.metadata.vgmdb_data?.discs!.push({
    disc_length: '',
    name: '',
    tracks: [
      {
        names: {
          Japanese: null,
          Romaji: null,
        },
        track_length: '',
      },
    ],
  })
}

if (props.post?.metadata?.vgmdb_data?.discs?.[0]?.tracks?.[0]?.names) {
  trackLangs.value = Object.keys(props.post.metadata.vgmdb_data.discs[0].tracks[0].names)
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
          :variant="post?.is_published ? undefined : 'outlined'"
          :type="canPublish && !form.is_published? undefined : 'submit'"
          form="storePost"
          :disabled="form.processing"
          :color="post?.is_published ? 'primary' : 'secondary'"
          :icon="!mdAndUp ? mdiContentSave : undefined"
          :prepend-icon="mdAndUp ? mdiContentSave : undefined"
          :text="mdAndUp ? 'Save' : undefined"
          @click.prevent="save"
        />
        <v-btn
          v-if="canPublish && !post?.is_published"
          form="storePost"
          type="submit"
          :disabled="form.processing"
          color="primary"
          :icon="!mdAndUp ? mdiSend : undefined"
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
      <div class="grid gap-4 grid-cols-1 md:grid-cols-12">
        <div class="md:col-span-8">
          <section class="mb-4">
            <v-card :rounded="smAndUp">
              <v-card-item title="Basic Information" />
              <v-divider />
              <v-card-text>
                <div class="flex items-start mb-4 gap-4">
                  <v-select
                    v-model="currentLang"
                    :multiple="false"
                    label="Title language"
                    return-object
                    variant="outlined"
                    :items="languages"
                    item-title="label"
                    :hide-details="true"
                    item-value="value"
                    class="flex-none w-36"
                  />
                  <v-select
                    v-model="form.metadata['post_type']"
                    :multiple="false"
                    label="Post type"
                    variant="outlined"
                    :items="postType"
                    hide-details="auto"
                    :error-messages="formErrors['metadata.post_type']"
                    class="flex-none w-48"
                  />
                </div>
                <v-text-field
                  v-model="form.title[currentLang.value]"
                  :label="`Title (${currentLang.value})`"
                  :error-messages="formErrors['title.' + currentLang]"
                  variant="outlined"
                  hide-details="auto"
                  class="mb-4"
                />
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
                  :error-messages="formErrors['title.native']"
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
          <section>
            <v-card
              class="mb-4"
              :rounded="smAndUp"
            >
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
                  v-for="(resource, i) in form.links"
                  :key="i"
                  variant="outlined"
                >
                  <v-card-item>
                    <template #prepend>
                      <v-btn
                        variant="plain"
                        :prepend-icon="mdiPencil"
                        density="comfortable"
                        color="secondary"
                        @click="editFilename(i)"
                      />

                      <v-btn
                        variant="plain"
                        :prepend-icon="mdiDelete"
                        density="comfortable"
                        color="error"
                        @click="deleteFilename(i)"
                      />
                    </template>
                    <v-card-title>
                      {{ resource.name }}
                    </v-card-title>
                    <template #append>
                      <v-btn
                        variant="plain"
                        :prepend-icon="mdiPlus"
                        density="comfortable"
                        color="primary"
                        @click.prevent="addLink(i)"
                      />
                    </template>
                  </v-card-item>
                  <template
                    v-for="(links, j) in resource.value"
                    :key="`link-${i}-${j}`"
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
                            v-model="form.links[i].value[j].name"
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
                            v-model="form.links[i].value[j].value"
                            placeholder="https://gudang.extonan.id/"
                            rows="2"
                            variant="outlined"
                            hide-details="auto"
                          />

                          <v-btn
                            color="error mt-4"
                            @click.prevent="deleteLink(i,j)"
                          >
                            Delete
                          </v-btn>
                        </v-col>
                      </v-row>
                    </v-card-text>
                    <v-divider v-if="j < resource.value.length" />
                  </template>
                  <v-card-text v-if="resource.value.length < 1">
                    No data.
                  </v-card-text>
                </v-card>
                <!--  -->
                <template v-if="form.links.length < 1">
                  No data.
                </template>
              </v-card-text>
            </v-card>
          </section>
          <v-expand-transition>
            <section v-if="form.metadata.vgmdb_data">
              <v-card
                class="mb-4"
                :disabled="form.processing"
              >
                <v-card-item>
                  <v-card-title>
                    Tracks Title Language
                  </v-card-title>
                  <template #append>
                    <v-btn
                      :disabled="form.processing"
                      color="primary"
                      :prepend-icon="mdiPlus"
                      @click="trackLangs!.push('')"
                    >
                      Add Lang
                    </v-btn>
                  </template>
                </v-card-item>

                <v-divider />
                <v-card-text class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <v-scroll-x-transition group>
                    <p
                      v-if="!trackLangs || trackLangs.length === 0"
                      key="NA"
                      class="mb-4 text-center col-span-md-2"
                    >
                      No data available
                    </p>
                    <v-text-field
                      v-for="(lang, index) in trackLangs"
                      :key="index"
                      v-model="trackLangs![index]"
                      :label="'Lang #' + (index + 1)"
                      :append-inner-icon="mdiClose"
                      variant="outlined"
                      @click:append-inner="deleteTrackLanguage(index)"
                    />
                  </v-scroll-x-transition>
                </v-card-text>
              </v-card>
              <!-- Disc -->
              <v-card
                :disabled="form.processing"
              >
                <v-card-item>
                  <v-card-title class="d-flex">
                    Discs
                  </v-card-title>
                  <template #append>
                    <v-btn
                      :disabled="form.processing"
                      color="primary"
                      :prepend-icon="mdiPlus"
                      text="Add Disc"
                      @click="addDisc"
                    />
                  </template>
                </v-card-item>
                <v-divider />
                <v-card-text
                  class="grid grid-cols-1 gap-2"
                  :class="{'p-2': form.metadata.vgmdb_data?.discs && form.metadata.vgmdb_data?.discs.length > 0}"
                >
                  <v-scroll-x-transition group>
                    <p
                      v-if="!form.metadata.vgmdb_data?.discs || form.metadata.vgmdb_data?.discs.length === 0"
                      key="NA"
                      class="mb-4 text-center"
                    >
                      No data available
                    </p>
                    <v-card

                      v-for="(disc, index) in form.metadata.vgmdb_data?.discs"
                      :key="index"
                      :disabled="form.processing"
                      variant="outlined"
                    >
                      <v-card-title class="d-flex">
                        Disc #{{ index + 1 }}
                        <v-spacer />
                        <v-btn
                          :disabled="form.processing"
                          size="small"
                          color="error"
                          @click="form.metadata.vgmdb_data?.discs!.splice(index, 1)"
                        >
                          <v-icon
                            start
                            :icon="mdiDelete"
                          />
                          Delete Disc #{{ index + 1 }}
                        </v-btn>
                      </v-card-title>
                      <v-card-text class="p-2 grid grid-cols-3 gap-2">
                        <v-text-field
                          v-model="disc.name"
                          class="col-span-2"
                          label="Disc Name"
                          variant="outlined"
                        />
                        <v-text-field
                          v-model="disc.disc_length"
                          label="Disc Length"
                          variant="outlined"
                        />
                        <v-scroll-x-transition group>
                          <div
                            v-for="(track, i) in disc.tracks"
                            :key="i"
                            class="col-span-3"
                          >
                            <v-card
                              :disabled="form.processing"
                              variant="outlined"
                            >
                              <v-card-item>
                                <v-card-title>
                                  Track #{{ i + 1 }}
                                </v-card-title>
                                <template #append>
                                  <v-btn
                                    :disabled="form.processing"
                                    color="error"
                                    :prepend-icon="mdiDelete"
                                    @click="disc.tracks!.splice(i, 1)"
                                  >
                                    Delete Track #{{ i + 1 }}
                                  </v-btn>
                                </template>
                              </v-card-item>
                              <v-card-text class="pa-2 d-grid grid-cols-3 gap-2">
                                <v-text-field
                                  v-for="(lang, j) in trackLangs"
                                  :key="j"
                                  v-model="track.names![lang]"
                                  class="col-span-2"
                                  :style="{order: j === 0 ? -1 : 2}"
                                  :disabled="!lang"
                                  :label="'Title ' + lang"
                                  variant="outlined"
                                />
                                <v-text-field
                                  v-model="track.track_length"
                                  style="order: 1"
                                  :disabled="!track.names || Object.keys(track.names).length === 0"
                                  label="Track Length"
                                  variant="outlined"
                                />
                              </v-card-text>
                            </v-card>
                          </div>
                        </v-scroll-x-transition>
                        <div
                          class="col-span-3"
                        >
                          <v-btn
                            :disabled="form.processing"
                            color="primary"
                            @click="addTrack(disc)"
                          >
                            Add Track
                          </v-btn>
                        </div>
                      </v-card-text>
                    </v-card>
                  </v-scroll-x-transition>
                </v-card-text>
              </v-card>
            </section>
          </v-expand-transition>
        </div>
        <div class="md:col-span-4">
          <v-card
            :rounded="smAndUp"
            class="mb-4"
          >
            <v-card-item>
              <v-card-title>
                Metadata
              </v-card-title>
            </v-card-item>
            <v-divider />
            <v-card-text>
              <v-text-field
                v-model="apiSearchId"
                label="VGMDB API"
                type="number"
                :error-messages="form.errors.metadata!"
                placeholder="10322"
                variant="outlined"
                hide-details="auto"
                @keydown.enter.prevent="fetchVgmdbData"
              />
            </v-card-text>
            <v-card-actions>
              <v-expand-x-transition>
                <v-btn
                  v-if="form.metadata"
                  :append-icon="mdiClose"
                  text="Clear"
                  @click="form.metadata.vgmdb_data = null"
                />
              </v-expand-x-transition>
              <v-spacer />
              <v-btn
                text="Autofill"
                @click="fetchVgmdbData"
              />
            </v-card-actions>
          </v-card>
          <div>
            <MediaManager
              v-model="form.thumbnail_item"
              class="mb-4"
              title="Media Thumbnail"
              :multiple="false"
            />
          </div>
        </div>
      </div>
    </v-form>
  </v-container>
</template>
