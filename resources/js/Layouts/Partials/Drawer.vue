<script setup lang="ts">
import { ref } from 'vue'

const drawer = ref<boolean | null>(null)
</script>

<template>
  <v-navigation-drawer
    v-model="drawer"
    temporary
    location="top"
  >
    <v-list>
      <v-list-item
        title="My Application"
        subtitle="Vuetify"
      />
      <v-divider />
      <v-list-item
        link
        title="List Item 1"
      />
      <v-list-item
        link
        title="List Item 2"
      />
      <v-list-item
        link
        title="List Item 3"
      />
    </v-list>
  </v-navigation-drawer>
</template>
