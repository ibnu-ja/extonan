<script setup lang="ts">
import { useUserStore } from '@/stores/userStore'
import { mdiPaletteSwatchVariant } from '@mdi/js'

const user = useUserStore()
</script>

<template>
  <v-menu>
    <template #activator="{ props }">
      <v-btn
        :icon="mdiPaletteSwatchVariant"
        v-bind="props"
      />
    </template>

    <v-card min-width="120">
      <v-card-text>
        <v-radio-group v-model="user.theme">
          <v-radio
            label="System"
            value="system"
          />
          <v-radio
            label="Dark"
            value="dark"
          />
          <v-radio
            label="Light"
            value="light"
          />
        </v-radio-group>
      </v-card-text>
    </v-card>
  </v-menu>
</template>

<style scoped>

</style>
