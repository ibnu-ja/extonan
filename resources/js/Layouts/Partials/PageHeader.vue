<script lang="ts" setup>

defineProps<{
  title: string
}>()

</script>

<template>
  <v-container
    class="py-0 my-4 mt-6 mt-lg-12"
  >
    <div class="d-flex justify-space-between align-end">
      <h1 class="text-h4 text-md-h3">
        {{ title }}
      </h1>
      <slot name="append" />
    </div>
  </v-container>
</template>
