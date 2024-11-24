<script lang="ts" setup>

import { Head, useForm } from '@inertiajs/vue3'
import { mdiContentSave, mdiDelete, mdiPencil, mdiPlus, mdiSend } from 'mdi-js-es'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
import { useDisplay } from 'vuetify'
import { useLanguages } from '@/composables/useLanguages'
import { computed, inject } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
import { openFormDialog } from '@/composables/useDialog'
import MediaManager from '@/Components/MediaManager/Index.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { MV, MVPostItem } from '@/types/mv'

defineOptions({
  name: 'AnimePostCreate',
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
  metadata: { post_type: route().params.type || 'mv' },
  thumbnail_item: null,
  title: {
    en: null,
    id: null,
    native: null,
    romaji: null,
  },
})

// assign props to form model
if (props.post) {
  form.title = props.post.title
  form.description = props.post.description
  form.links = props.post.links
  form.metadata = props.post.metadata
  form.thumbnail_item = props.post.thumbnail_item
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
          </div>
        </div>
      </div>
    </v-form>
  </v-container>
</template>
