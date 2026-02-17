<script setup lang="ts">
import { computed, useSlots } from 'vue'

defineEmits(['submitted'])

const hasActions = computed(() => !!useSlots().actions)
</script>

<template>
  <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
    <div>
      <slot name="title" />

      <p class="">
        <slot name="description" />
      </p>
    </div>
    <div class="col-span-2">
      <v-card class="elevation-2">
        <v-form @submit.prevent="$emit('submitted')">
          <v-card-title
            v-if="$slots.formTitle"
            class="flex"
          >
            <slot name="formTitle" />
          </v-card-title>

          <slot name="form" />

          <v-card-actions v-if="hasActions">
            <slot name="actions" />
          </v-card-actions>
        </v-form>
      </v-card>
    </div>
  </div>
</template>
