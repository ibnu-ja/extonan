<script lang="ts" setup>

import { onBeforeMount } from 'vue'
import { useTheme } from 'vuetify'
import AppBar from '@/Layouts/Partials/AppBar.vue'
import Drawer from '@/Layouts/Partials/Drawer.vue'

const vuetifyTheme = useTheme()

onBeforeMount(() => {
  vuetifyTheme.global.name.value = 'dark'
})

</script>
<template>
  <v-layout>
    <AppBar />
    <Drawer />
    <v-main>
      <slot />
    </v-main>
  </v-layout>
</template>
