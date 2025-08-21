<script lang="ts" setup>

import { AnimeData, EpisodeData, Resource } from '@/types/anime'
import { Head, Link as InertiaLink, router, useForm } from '@inertiajs/vue3'
import {
  mdiChevronDown,
  mdiChevronUp,
  mdiContentSave,
  mdiDelete,
  mdiOpenInNew,
  mdiPencil,
  mdiPlus,
  mdiSend,
} from 'mdi-js-es'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
import { useDisplay } from 'vuetify'
import { TranslatableField } from '@/types/formHelper'
import { useLanguages } from '@/composables/useLanguages'
import Metadata from '@/Pages/Anime/Partials/Metadata.vue'
import { computed, inject, ref } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
import { openConfirmationDialog, openFormDialog } from '@/composables/useDialog'
import MediaManager from '@/Components/MediaManager/Index.vue'
import { Media } from '@/types'
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({
  name: 'AnimePostCreate',
  layout: AppLayout,
})

const props = defineProps<{
  anime: AnimeData
  canPublish: boolean
  post?: EpisodeData & {
    links: Resource[]
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    metadata: Record<string, any>
    thumbnail_item: Media | null
  }
}>()
const route = inject('route') as typeof ziggyRoute
const { mdAndUp } = useDisplay()
type PostForm = {
  title: TranslatableField
  description: TranslatableField
  is_published: boolean
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  metadata: Record<string, any>
  links: Resource[]
  thumbnail_item: Media | null
}

// @ts-ignore
const form = useForm<PostForm>({
  title: {
    en: null,
    id: null,
    native: null,
    romaji: null,
  },
  is_published: props.post?.is_published ?? false,
  description: {
    en: null,
    id: null,
  },
  metadata: {
    post_type: 'tv',
  },
  links: [],
  thumbnail_item: null,
})

if (props.post) {
  form.title = props.post.title
  form.description = props.post.description
  form.links = props.post.links
  form.metadata = props.post.metadata
  form.thumbnail_item = props.post.thumbnail_item
}

const submit = () => {
  if (props.post) {
    form.put(route('post.update', [props.anime, props.post]))
  } else {
    form.post(route('post.store', props.anime))
  }
}

const publish = () => {
  form.is_published = true
  submit()
}

const save = () => {
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

const { translate } = useLanguages()

const title = props.post ? 'Editing ' + translate(props.post?.title) : 'Create Episode'

const deletePost = async () => {
  try {
    const confirmed = await openConfirmationDialog('Are you sure want to delete this item?')
    if (confirmed && props.post?.id && props.anime.id) {
      router.delete(route('post.destroy', [props.anime.id, props.post.id]))
    }
  } catch {
    //
  }
}

const postType = [
  {
    value: 'bd',
    title: 'Blu-ray BOX/DISC',
  },
  {
    value: 'tv',
    title: 'TV Ep',
  },
  {
    value: 'movie',
    title: 'Movie',
  },
]

// eslint-disable-next-line @typescript-eslint/no-explicit-any
const formErrors = computed(() => form.errors as any)

const showAnimeImage = ref(false)

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
  <v-container
    :max-width="1800"
    class="p-0 md:p-4"
  >
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
                  <v-text-field
                    v-model="form.metadata.ep_no"
                    variant="outlined"
                    :hide-details="true"
                    label="Ep No."
                    placeholder="1-4"
                    class="flex-none w-32"
                  />
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
                    label="Ep. type"
                    variant="outlined"
                    :items="postType"
                    hide-details="auto"
                    :error-messages="formErrors['metadata.post_type']"
                    class="flex-none w-36"
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
                  v-for="(resource, i) in form.links"
                  :key="i"
                  variant="outlined"
                >
                  <v-card-item>
                    <template #prepend>
                      <v-btn
                        variant="plain"
                        :icon="true"
                        density="comfortable"
                        color="secondary"
                        @click="editFilename(i)"
                      >
                        <v-icon
                          :icon="mdiPencil"
                          size="20"
                        />
                      </v-btn>

                      <v-btn
                        variant="plain"
                        :icon="true"
                        density="comfortable"
                        color="error"
                        @click="deleteFilename(i)"
                      >
                        <v-icon
                          :icon="mdiDelete"
                          size="20"
                        />
                      </v-btn>
                    </template>
                    <v-card-title>
                      {{ resource.name }}
                    </v-card-title>
                    <template #append>
                      <v-select
                        v-model="resource.type"
                        variant="solo-inverted"
                        density="compact"
                        hide-details
                        :items="[{title: 'Link DL', value: 'link'}, {title: 'URL Saluran', value: 'saluran'}, { title: 'Embed HTML', value: 'embed'}]"
                      />
                      <v-btn
                        variant="plain"
                        :icon="true"
                        density="comfortable"
                        :prepend-icon="mdiPlus"
                        color="primary"
                        @click.prevent="addLink(i)"
                      >
                        <v-icon
                          :icon="mdiPlus"
                          size="20"
                        />
                      </v-btn>
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
        </div>
        <div class="md:col-span-4">
          <div>
            <!--TODO-->
            <MediaManager
              v-model="form.thumbnail_item"
              class="mb-4"
              title="Media Thumbnail"
              :multiple="false"
            />
            <v-card :rounded="smAndUp">
              <v-expand-transition>
                <v-img
                  v-if="showAnimeImage"
                  :src="anime.metadata.coverImage.extraLarge"
                  :lazy-src="anime.metadata.coverImage.medium"
                />
              </v-expand-transition>
              <v-card-item>
                <v-card-title>
                  Anime
                </v-card-title>
                <template #append>
                  <v-tooltip text="Show image">
                    <template #activator="{props: propss}">
                      <v-btn
                        v-bind="propss"
                        variant="text"
                        :icon="showAnimeImage ? mdiChevronUp : mdiChevronDown"
                        @click="showAnimeImage = !showAnimeImage"
                      />
                    </template>
                  </v-tooltip>
                </template>
              </v-card-item>
              <v-divider />
              <v-card-text>
                <!-- template -->
                <div>
                  <v-list-subheader>
                    Title
                  </v-list-subheader>
                  <InertiaLink :href="route('anime.show', anime.id)">
                    {{ anime.title.en }}
                  </InertiaLink>
                </div>
                <div>
                  <v-list-subheader>
                    Links
                  </v-list-subheader>
                  <div class="flex gap-1">
                    <v-chip
                      prepend-avatar="https://upload.wikimedia.org/wikipedia/commons/6/61/AniList_logo.svg"
                      :href="`https://anilist.co/anime/${anime.metadata.id}`"
                      target="_blank"
                    >
                      Anilist
                      <v-icon :icon="mdiOpenInNew" />
                    </v-chip>
                    <v-chip
                      prepend-avatar="https://upload.wikimedia.org/wikipedia/commons/9/9b/MyAnimeList_favicon.svg"
                      :href="`https://myanimelist.net/anime/${anime.metadata.idMal}`"
                      target="_blank"
                    >
                      MyAnimelist
                      <v-icon :icon="mdiOpenInNew" />
                    </v-chip>
                  </div>
                </div>
              </v-card-text>

              <v-expand-transition>
                <div v-if="anime.metadata">
                  <v-divider />
                  <v-card-text>
                    <Metadata
                      :data="anime.metadata"
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
