<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3'
import { mdiClose } from '@mdi/js'
import { computed, ref, watch } from 'vue'

const message = computed(() => usePage().props.jetstream?.flash?.banner)
const show = ref(!!message.value)

const style = computed(() => usePage().props.jetstream?.flash?.bannerStyle as string || 'success')

watch(message, () => {
  show.value = true
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
