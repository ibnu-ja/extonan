<script lang="ts" setup>

defineProps<{
  length: number
  progress: number
}>()
</script>

<template>
  <v-card
    variant="outlined"
    class="d-flex flex-column justify-center align-center"
    style="height: 100%;"
  >
    <div>
      Uploading {{ length }} file(s)
    </div>
    <v-progress-circular
      :model-value="progress"
      :size="130"
      :width="9"
    >
      <strong class="text-lg">{{ progress }}%</strong>
    </v-progress-circular>
  </v-card>
</template>
