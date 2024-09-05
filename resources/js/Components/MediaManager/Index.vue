<script lang="ts" setup>
import { mdiDelete, mdiImage } from '@mdi/js'
import axios from 'axios'
import prettyBytes from 'pretty-bytes'
import { inject, onMounted, ref, watch } from 'vue'

import { openConfirmationDialog } from '@/composables/useDialog'
// import { openErrorToast } from '@/composables/useSnackbar'
import { Media, PaginatedResponse } from '@/types'

// import Heading from '../Heading.vue'
import MediaRemoteUploader from './MediaRemoteUploader.vue'
import MediaSelector from './MediaSelector.vue'
import MediaUploader from './MediaUploader.vue'
import { route as ziggyRoute } from 'ziggy-js'
import dayjs from 'dayjs'
import MediaGridView from '@/Components/MediaManager/MediaGridView.vue'

const emit = defineEmits([
  'update:modelValue',
])

interface Props {
  multiple?: boolean
  title?: string
  disabled?: boolean
  modelValue?: Media[] | Media | null
}

const props = withDefaults(defineProps<Props>(), {
  multiple: false,
  title: 'Add Media',
  disabled: false,
  modelValue: undefined,
})
// const modelValue = defineModel<Media[] | Media | undefined>()

const media = ref<PaginatedResponse<Media>>()
const drawer = ref<boolean>()

const tab = ref<string>()
const date = ref<string>('')
const months = ref<string[]>([])
const latestSelected = ref<Media | null>(null)
const selected = ref<Media | Media[] | null>()

const route = inject('route') as typeof ziggyRoute
const getMedia = async () => {
  const url = route('media.index', {
    dir: date.value!,
  })
  const { data } = await axios.get<PaginatedResponse<Media>>(url)
  media.value = data
}

const getMediaMonth = async () => {
  const url = route('media.months')
  const { data } = await axios.get<string[]>(url)
  months.value = data
}

const dialog = ref(false)

onMounted(async () => {
  await getMediaMonth()
  selected.value = props.modelValue
})

async function deleteMedia(media: Media) {
  // open confirmation dialog
  // if confirmed, delete media
  const confirmed = await openConfirmationDialog('Hapus Item', 'Anda Yakin ingin Menghapus Item?')
  if (confirmed) {
    // inertia delete item
    await axios.delete(route('media.destroy', media.id))
    await getMedia()
    // TODO open snackbar toast
    alert('item berhasil dihapus')
    // openErrorToast('Item Berhasil Dihapus', 'success')
  }
}

watch(date, async () => {
  await getMedia()
})

watch(media, async () => {
  await getMediaMonth()
})
const save = () => {
  dialog.value = false
  emit('update:modelValue', selected.value)
}
</script>

<template>
  <v-card :disabled="disabled">
    <v-card-item>
      <v-card-title class="d-flex">
        {{ title }}
      </v-card-title>
      <template #append>
        <v-btn
          :disabled="disabled"
          color="primary"
          :prepend-icon="mdiImage"
          @click="dialog = !dialog"
        >
          Add Media
          <!-- {{ selected.length === 0 ? 'Add' : `${selected.length} Selected` }} -->
        </v-btn>
      </template>
    </v-card-item>
    <v-card-text
      v-if="modelValue"
      class="d-grid"
      :class="multiple ? 'grid-cols-2 grid-cols-lg-3 gap-4' : 'grid-cols-1'"
    >
      <MediaGridView :images="modelValue" />
    </v-card-text>
    <v-card-text v-else>
      No media selected.
    </v-card-text>
    <v-dialog
      v-model="dialog"
      persistent
    >
      <v-card
        width="100%"
        height="100vh"
      >
        <v-layout>
          <v-navigation-drawer
            v-model="drawer"
            :order="0"
          >
            <div
              class="pa-2"
            >
              <h5 class="text-h5">
                Media Details
              </h5>
              <template
                v-if="latestSelected"
              >
                <ul class="ml-6">
                  <li>
                    <b class="font-weight-bold">Filename: </b>
                    <div> {{ latestSelected.filename }}.{{ latestSelected.extension }}</div>
                  </li>
                  <li>
                    <b class="font-weight-bold">Created: </b>
                    <div>
                      {{ dayjs(latestSelected.created_at) }}
                    </div>
                  </li>
                  <li>
                    <b class="font-weight-bold">Size: </b>
                    <div>
                      {{ prettyBytes(latestSelected.size) }}
                    </div>
                  </li>
                </ul>
                <!-- TODO center button -->
                <v-btn
                  class="mt-2 text-center"
                  color="error"
                  :append-icon="mdiDelete"
                  @click="deleteMedia(latestSelected!)"
                >
                  Delete
                </v-btn>
              </template>
              <div
                v-else
                class="mt-2"
              >
                No file selected
              </div>
            </div>

            <v-divider />
            <v-list
              nav
              density="compact"
            >
              <v-list-item-subtitle class="mb-2">
                Uploaded months
              </v-list-item-subtitle>
              <v-list-item
                v-for="month in months"
                :key="month"
                :title="month"
                :active="date === month"
                color="primary"
                @click="date === month ? date = '' : date = month"
              />
            </v-list>
          </v-navigation-drawer>

          <v-app-bar
            scroll-behavior="hide"
            density="compact"
            elevation="0"
          >
            <v-app-bar-nav-icon
              class="d-lg-none"
              @click.stop="drawer = !drawer"
            />
            <v-tabs
              v-model="tab"
              density="compact"
            >
              <v-tab value="upload">
                Upload File(s)
              </v-tab>
              <v-tab value="remote">
                Remote Upload
              </v-tab>
            </v-tabs>
          </v-app-bar>
          <v-main>
            <v-container class="scroll pb-0">
              <v-window
                v-model="tab"
                class="mb-4"
              >
                <v-window-item value="upload">
                  <MediaUploader
                    :multiple="multiple"
                    @uploaded="async () => await getMedia()"
                  />
                </v-window-item>

                <v-window-item value="remote">
                  <MediaRemoteUploader
                    @uploaded="async () => await getMedia()"
                  />
                </v-window-item>
              </v-window>
              <MediaSelector
                v-model="selected"
                :media="media!"
                :multiple="multiple"
                @vue:mounted="async () => await getMedia()"
                @selected="latestSelected = $event"
              />
            </v-container>
          </v-main>
        </v-layout>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            @click="dialog = !dialog"
          >
            Cancel
          </v-btn>
          <v-btn @click="save">
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-card>
</template>

<style scoped>
.scroll {
  height: calc(100vh - 100px);
  overflow-y: auto;
}
</style>
