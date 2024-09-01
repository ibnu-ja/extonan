<script lang="ts" setup>

import { computed, ref, watchEffect } from 'vue'
import { useTheme } from 'vuetify'
import AppBar from '@/Layouts/Partials/AppBar.vue'
import Drawer from '@/Layouts/Partials/Drawer.vue'
import { useUserStore } from '@/stores'
import { usePreferredDark } from '@vueuse/core'
import { BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import Banner from '@/Components/Banner.vue'
import Dialog from '@/Components/Dialog.vue'
import Footer from '@/Layouts/Partials/Footer.vue'

const theme = useTheme()
const user = useUserStore()
const systemTheme = computed(() => usePreferredDark().value ? 'dark' : 'light')

watchEffect(() => {
  theme.global.name.value = (
    user.theme === 'system' ? systemTheme.value : user.theme
  )
})
const drawer = ref<boolean | undefined>()

defineProps<{
  breadcrumbs?: BreadcrumbItem[]
  title?: string
}>()

</script>
<template>
  <Head :title="title" />
  <v-app>
    <Banner />
    <Dialog />
    <AppBar
      v-model:drawer="drawer"
      :breadcrumbs="breadcrumbs"
    />
    <Drawer v-model="drawer" />
    <v-main>
      <slot />
      <Footer />
    </v-main>
  </v-app>
</template>
