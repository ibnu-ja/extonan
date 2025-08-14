<script lang="ts" setup>
import { onMounted, onUnmounted, ref } from 'vue'

const props = defineProps({
  multiple: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits([
  'files-dropped',
])

const active = ref(false)
let inActiveTimeout: ReturnType<typeof setTimeout> // add a variable to hold the timeout key

function setActive() {
  active.value = true
  clearTimeout(inActiveTimeout) // clear the timeout
}
function setInactive() {
  // wrap it in a `setTimeout`
  inActiveTimeout = setTimeout(() => {
    active.value = false
  }, 50)
}

function onDrop(e: DragEvent) {
  setInactive()
  // if multiple
  if (props.multiple) {
    emit('files-dropped', e.dataTransfer!.files)
  } else {
    emit('files-dropped', e.dataTransfer!.files[0])
  }
}
function preventDefaults(e: Event) {
  e.preventDefault()
}

const events = ['dragenter', 'dragover', 'dragleave', 'drop']

onMounted(() => {
  events.forEach((eventName) => {
    document.body.addEventListener(eventName, preventDefaults)
  })
})

onUnmounted(() => {
  events.forEach((eventName) => {
    document.body.removeEventListener(eventName, preventDefaults)
  })
})

</script>

<template>
  <v-card
    variant="outlined"
    style="height: 100%;"
    class="p-4 d-flex align-center justify-center"
    :data-active="active"
    @dragenter.prevent="setActive"
    @dragover.prevent="setActive"
    @dragleave.prevent="setInactive"
    @drop.prevent="onDrop"
  >
    <div class="text-center">
      <slot :drop-zone-active="active" />
    </div>
  </v-card>
</template>
