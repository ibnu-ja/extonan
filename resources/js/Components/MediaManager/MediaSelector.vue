<!-- eslint-disable no-unused-vars -->
<script lang="ts" setup>
import axios from 'axios'
import { ref, watch } from 'vue'

import { Media, PaginatedResponse } from '@/types'
import { imageLink } from '@/composables/useImageLink'
import { mdiCheck } from '@mdi/js'

const props = withDefaults(defineProps<{
  media: PaginatedResponse<Media>
  modelValue?: Media[] | Media | null
  multiple: boolean
}>(), {
  modelValue: null,
  multiple: false,
})

const emit = defineEmits(['update:modelValue', 'selected'])

// for storing paginated media from API
const currentMedia = ref<PaginatedResponse<Media> | null>(null)
// for storing all lazy-loaded media
const loaded = ref<Media[]>([])
// for showing media info on side panel
// const latestSelected = ref<Media | null>()

async function loadMore(url: string | undefined) {
  if (!url) throw new Error('No more media to load')
  // if (date) {
  //   url = `${url}`
  // }
  const { data } = await axios.get<PaginatedResponse<Media>>(`${url}`)
  currentMedia.value = data
  loaded.value = [...loaded.value, ...data.data]
}

// function onMediaSelect (item: Media) {
//   // TODO refactor select logic
//   if (!multiple) {
//     if (modelValue === null || (!Array.isArray(modelValue) && modelValue!.id !== item.id)) {
//       emit('update:modelValue', item)
//       emit('selected', item)
//       return
//     }
//     emit('update:modelValue', null)
//   } else {
//     console.log('array')
//     if (!Array.isArray(modelValue)) {
//       throw new Error('selected is not an array')
//     }
//     if (modelValue!.some((i) => i.id === item.id)) {
//       emit('update:modelValue', modelValue!.filter((i) => i.id !== item.id))
//     } else {
//       emit('update:modelValue', [...modelValue, item])
//       emit('selected', item)
//     }
//   }
// }

// onUpdated(async () => {
//   console.log('updated', media)
//   await loadMore(media.path)
// })

function onMediaSelect(item: Media) {
  if (!props.multiple) {
    handleSingleSelect(item)
  } else {
    handleMultipleSelect(item)
  }
}

function handleSingleSelect(item: Media) {
  const isItemSelected = props.modelValue !== null && (Array.isArray(props.modelValue) || props.modelValue!.id === item.id)
  if (!isItemSelected) {
    emit('update:modelValue', item)
    emit('selected', item)
    return
  }
  emit('update:modelValue', null)
  emit('selected', null)
}

function handleMultipleSelect(item: Media) {
  if (!Array.isArray(props.modelValue)) {
    throw new Error('Media Selector v-model is not an array')
  }

  // TODO maybe add check if multiple

  if (isItemSelected(item)) {
    emit('update:modelValue', props.modelValue.filter(i => i.id !== item.id))
    emit('selected', null)
  } else {
    emit('update:modelValue', [...props.modelValue, item])
    emit('selected', item)
  }
}

function isItemSelected(item: Media): boolean {
  if (!Array.isArray(props.modelValue)) {
    throw new Error('Media Selector v-model is not an array')
  }
  return props.modelValue!.some(i => i.id === item.id)
}

watch(() => props.media, (value) => {
  currentMedia.value = value!
  loaded.value = value!.data
})

const selectedIndex = (item: Media) => {
  if (Array.isArray(props.modelValue)) {
    return props.modelValue!.findIndex(({ id }) => id === item.id) + 1
  } else {
    return props.modelValue?.id === item.id
  }
}
</script>

<template>
  <div>
    <h3 class="text-h4">
      Recent Media
    </h3>
    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
      <v-img
        v-for="item in loaded"
        :key="item.id"
        v-ripple
        :aspect-ratio="1"
        cover
        class="text-right p-2 rounded"
        :src="imageLink(item)?.medium"
        style="cursor: pointer"
        @click="onMediaSelect(item)"
      >
        <!-- <v-btn
            :icon="mdiCheck"
            color="primary"
          /> -->
        <template v-if="selectedIndex(item)">
          <v-btn
            v-if="multiple"
            :icon="true"
            color="primary"
          >
            {{ selectedIndex(item) }}
          </v-btn>
          <v-btn
            v-else
            :icon="mdiCheck"
            color="primary"
          />
        </template>
        <template #placeholder>
          <v-row
            class="fill-height m-0"
            align="center"
            justify="center"
          >
            <v-progress-circular
              indeterminate
              color="grey-lighten-5"
            />
          </v-row>
        </template>
      </v-img>
    </div>
    <div
      v-if="currentMedia?.next_page_url"
      class="text-center"
    >
      <v-btn
        variant="text"
        large
        @click="loadMore(currentMedia?.next_page_url)"
      >
        Load More
      </v-btn>
    </div>
  </div>
</template>
