<script lang="ts" setup>
import { computed } from 'vue'
import { mdiCloseBox } from 'mdi-js-es'

const props = withDefaults(defineProps<{
  state?: string | null
}>(), {
  state: null,
})

const modelValue = computed(() => {
  return props.state == 'in'
})

const indeterminate = computed(() => {
  return props.state == 'notIn'
})
</script>

<template>
  <v-checkbox-btn
    :model-value
    :indeterminate
    :indeterminate-icon="mdiCloseBox"
    @change.prevent
    @click.prevent
  />
</template>
