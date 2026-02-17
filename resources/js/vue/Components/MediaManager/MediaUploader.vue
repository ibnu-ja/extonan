<script lang="ts" setup>
import axios, { AxiosProgressEvent } from 'axios'
import { inject, ref } from 'vue'

// import { openErrorToast } from '@/composables/useSnackbar'
// import { ziggySymbol } from '@/symbols/ziggy'
// import Heading from '../Heading.vue'
import DropZone from './DropZone.vue'
import UploadProgress from './UploadProgress.vue'
import { route as ziggyRoute } from 'ziggy-js'

const progress = ref()
const uploading = ref(false)

// for storing file events
const files = ref<File[]>([])

const input = ref<HTMLInputElement | null>(null)

const route = inject('route') as typeof ziggyRoute

const { multiple = false } = defineProps<{
  multiple?: boolean
}>()

const emit = defineEmits(['uploaded'])

function onInputChange(e: Event) {
  addFiles((e.target! as HTMLInputElement)!.files!)
  input!.value = null
}

function selectNewFile() {
  input.value!.click()
}

// to server
async function addFiles(newFiles: FileList | File) {
  try {
    uploading.value = true
    const formData = new FormData()
    // check if newFiles is array, if not, convert it to array
    if (newFiles instanceof FileList) {
      for (let i = 0; i < newFiles.length; i++) {
        files.value.push(newFiles[i])
        formData.append('media[]', newFiles[i])
      }
    } else {
      files.value = [newFiles]
      formData.append('media[]', newFiles)
    }
    await axios.post(route('media.store'), formData, {
      onUploadProgress: (progressEvent: AxiosProgressEvent) => {
        progress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total!)
      },
    })
    uploading.value = false
    files.value = []
    emit('uploaded')
    // tab.value = 'media'
  } catch (error) {
    // TODO: handle error using snackbar
    if (error instanceof Error) {
      alert(error.message)
      // openErrorToast(error.message)
    } else {
      alert('Something went wrong')
      // openErrorToast()
      console.error(error)
    }
  } finally {
    uploading.value = false
  }
}

</script>

<template>
  <div>
    <UploadProgress
      v-if="uploading"
      :progress="progress"
      :length="files.length"
    />

    <DropZone
      v-else
      v-slot="{ dropZoneActive }"
      class="mt-4"
      :multiple="multiple"
      @files-dropped="addFiles"
    >
      <input
        id="file-input"
        ref="input"
        style="display: none;"
        type="file"
        :multiple="multiple"
        @change="onInputChange"
      >
      <h4 class="text-h6">
        Drop File(s) to Upload
      </h4>
      <div v-if="!dropZoneActive">
        <div class="mb-2">
          or
        </div>
        <v-btn
          variant="outlined"
          @click.prevent="selectNewFile"
        >
          Select File(s)
        </v-btn>
      </div>
    </DropZone>
  </div>
</template>
