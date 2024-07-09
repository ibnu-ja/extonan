<script lang="ts" setup>

import { computed, watchEffect } from 'vue'
import { useTheme } from 'vuetify'
import AppBar from '@/Layouts/Partials/AppBar.vue'
import Drawer from '@/Layouts/Partials/Drawer.vue'
import { useUserStore } from '@/stores/userStore'
import { usePreferredDark } from '@vueuse/core'
import { BreadcrumbItem } from '@/types'

const theme = useTheme()
const user = useUserStore()
const systemTheme = computed(() => usePreferredDark().value ? 'dark' : 'light')

watchEffect(() => {
  theme.global.name.value = (
    user.theme === 'system' ? systemTheme.value : user.theme
  )
})

defineProps<{
  breadcrumbs?: BreadcrumbItem[]
}>()

</script>
<template>
  <v-layout>
    <AppBar :breadcrumbs="breadcrumbs" />
    <Drawer />
    <v-main>
      <!-- Page Heading -->
      <v-container
        v-if="$slots.header"
      >
        <header>
          <slot name="header" />
        </header>
      </v-container>
      <slot />
    </v-main>
  </v-layout>
</template>
