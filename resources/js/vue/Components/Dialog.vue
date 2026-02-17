<script lang="ts" setup>

import { ref } from 'vue'

import { closeDialog, dialogModel, dialogRef } from '@/composables/useDialog'

const value = ref<string | null>(null)
const valid = ref(false)

function submit() {
  closeDialog(value.value)
  value.value = null
}

function required(value: string) {
  return !!value || 'Required'
}

</script>

<template>
  <v-dialog
    v-model="dialogModel"
    max-width="400"
    persistent
  >
    <v-form
      v-if="dialogRef?.textFieldProps"
      v-model="valid"
      @submit.prevent="submit"
    >
      <v-card max-width="400">
        <v-card-item
          v-if="dialogRef?.title"
          :title="dialogRef.title"
        />
        <v-divider />
        <v-card-text>
          <p
            v-if="dialogRef?.desc"
            class="mb-4"
          >
            {{ dialogRef.desc }}
          </p>
          <v-text-field
            v-model="value"
            :variant="dialogRef?.textFieldProps.variant"
            :label="dialogRef?.textFieldProps.label"
            :placeholder="dialogRef?.textFieldProps.placeholder"
            required
            :rules="[required]"
            :autocomplete="dialogRef?.textFieldProps.autocomplete"
            hide-details="auto"
            clearable
          />
        </v-card-text>
        <v-divider />
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            @click="closeDialog()"
          >
            Close
          </v-btn>
          <v-btn
            :disabled="!valid"
            color="primary"
            type="submit"
          >
            OK
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-form>
    <template v-else>
      <v-card>
        <v-card-item
          v-if="dialogRef?.title"
          :title="dialogRef.title"
        />
        <!-- <v-card-title v-if="$slots.title">
        <slot name="title" />
      </v-card-title> -->
        <v-card-text>
          <p
            v-if="dialogRef?.desc"
            class="mb-4"
          >
            {{ dialogRef.desc }}
          </p>
        </v-card-text>
        <v-divider />
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            @click="closeDialog()"
          >
            Close
          </v-btn>
          <v-btn
            color="primary"
            @click="closeDialog(true)"
          >
            OK
          </v-btn>
        </v-card-actions>
      </v-card>
    </template>
  </v-dialog>
</template>
