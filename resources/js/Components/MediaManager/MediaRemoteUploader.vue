<script lang="ts" setup>
import axios, { AxiosProgressEvent } from 'axios'
import { inject, ref } from 'vue'

// import { openErrorToast } from '@/scripts/composables/useSnackbar'
// import { SharedData } from '@/types/inertia-page'
// import Validation from '@/views/components/socialstream/Validation.vue'
import UploadProgress from './UploadProgress.vue'
import { route as ziggyRoute } from 'ziggy-js'

const urls = ref<string>('')
const route = inject('route') as typeof ziggyRoute

const progress = ref<number>(0)
const processing = ref(false)
const errorMessage = ref<string>('')
const splitted = ref<string[]>([])

// const inertiaError = usePage().props.errors!
const emit = defineEmits(['uploaded'])

async function upload() {
  try {
    processing.value = true
    splitted.value = urls.value.split(/\r?\n|\r|\n/g).filter(url => (url.trim()))
    // check if each splitted array is valid url
    const validated = splitted.value.filter(url => isValidImageLink(url))
    if (validated.length === 0) {
      throw new Error('There\'s no valid Image URL')
    }
    await axios.post(route('media.store'), {
      url: validated,
    }, {
      onUploadProgress: (e: AxiosProgressEvent) => {
        progress.value = Math.round((e.loaded * 100) / e.total!)
      },
    })
    emit('uploaded')
  } catch (error) {
    if (axios.isAxiosError(error)) {
      errorMessage.value = error.response?.data.message
    } else if (error instanceof Error) {
      alert(error.message)
      // openErrorToast(error.message)
    } else {
      alert('Unknown Error')
      console.error(error)
    }
  } finally {
    urls.value = ''
    splitted.value = []
    progress.value = 0
    processing.value = false
  }
}
function checkURL(url: string) {
  return (url.match(/\.(jpeg|jpg|gif|png)$/) != null)
}

function isValidImageLink(urlToCheck: string) {
  let url: string | URL
  try {
    url = new URL(urlToCheck)
    // check if url is image
    if (!checkURL(urlToCheck)) {
      throw new Error('URL not an image')
    }
  } catch (error) {
    console.error(error)
    return false
  }
  return url.protocol === 'http:' || url.protocol === 'https:'
}

</script>

<template>
  <UploadProgress
    v-if="processing"
    :progress="progress"
    :length="splitted.length"
  />
  <v-form
    :disabled="processing"
    @submit.prevent="upload"
  >
    <!--<Validation :show="Object.keys(inertiaError)" />-->
    <v-textarea
      v-model="urls"
      rows="2"
      :disabled="processing"
      :error-messages="errorMessage"
      class="mt-4"
      label="One URL per line"
      variant="outlined"
    />
    <v-btn
      :disabled="processing"
      variant="outlined"
      type="submit"
    >
      Upload
    </v-btn>
  </v-form>
</template>
