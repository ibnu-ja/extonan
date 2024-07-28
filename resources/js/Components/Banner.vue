<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3'
import { mdiClose } from '@mdi/js'
import { ref, watchEffect } from 'vue'

const page = usePage()
const show = ref(false)
const style = ref('success')
const message = ref('')

watchEffect(async () => {
  style.value = page.props.jetstream.flash?.bannerStyle as string || 'success'
  message.value = page.props.jetstream.flash?.banner as string || ''
  if (message.value && message.value !== '') {
    show.value = true
  }
})
</script>

<template>
  <v-snackbar
    v-model="show"
    multi-line
    :color="style"
    closable
  >
    {{ message }}

    <template #actions>
      <v-btn
        size="small"
        :icon="mdiClose"
        variant="text"
        @click="show = false"
      />
    </template>
  </v-snackbar>
</template>
