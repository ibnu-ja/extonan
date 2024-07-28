<script lang="ts" setup>

import { computed, watchEffect } from 'vue'
import { useTheme } from 'vuetify'
import AppBar from '@/Layouts/Partials/AppBar.vue'
import Drawer from '@/Layouts/Partials/Drawer.vue'
import { useUserStore } from '@/stores/userStore'
import { usePreferredDark } from '@vueuse/core'
import { BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import Banner from '@/Components/Banner.vue'

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
  title?: string
}>()

</script>
<template>
  <Head :title="title" />
  <v-app>
    <Banner />
    <AppBar :breadcrumbs="breadcrumbs" />
    <Drawer />
    <v-main>
      <!-- Page Heading -->
      <!-- mt-md-6-->
      <v-container
        v-if="$slots.header"
        class="py-0 my-4 mt-6 mt-lg-12"
      >
        <header>
          <slot name="header" />
        </header>
      </v-container>
      <slot />
    </v-main>
  </v-app>
</template>
