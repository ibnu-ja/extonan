<script lang="ts" setup>
import { computed, useSlots } from 'vue'

const slots = useSlots()

const hasActions = computed(() => !!slots.actions)
const hasStatus = computed(() => !!slots.status)

</script>

<template>
  <div class="d-grid grid-cols-1 gap-4 grid-cols-md-3">
    <div>
      <slot name="title" />

      <p class="">
        <slot name="description" />
      </p>
    </div>
    <div class="col-span-2">
      <v-card class="elevation-2">
        <v-card-title
          v-if="hasStatus"
          class="flex"
        >
          <slot name="status" />
        </v-card-title>
        <slot name="content" />
        <v-card-actions v-if="hasActions">
          <slot name="actions" />
        </v-card-actions>
      </v-card>
    </div>
  </div>
</template>
