<script lang="ts" setup>

import { computed, watchEffect } from 'vue'
import { useTheme } from 'vuetify'
import AppBar from '@/Layouts/Partials/AppBar.vue'
import Drawer from '@/Layouts/Partials/Drawer.vue'
import { useUserStore } from '@/stores/userStore'
import { usePreferredDark } from '@vueuse/core'

const theme = useTheme()
const user = useUserStore()
const systemTheme = computed(() => usePreferredDark().value ? 'dark' : 'light')

watchEffect(() => {
  theme.global.name.value = (
    user.theme === 'system' ? systemTheme.value : user.theme
  )
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
