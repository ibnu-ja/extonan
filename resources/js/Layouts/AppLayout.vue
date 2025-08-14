<script lang="ts" setup>

import { ref, watchEffect } from 'vue'
import AppBar from '@/Layouts/Partials/AppBar.vue'
import Drawer from '@/Layouts/Partials/Drawer.vue'
import { BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import Banner from '@/Components/Banner.vue'
import Dialog from '@/Components/Dialog.vue'
import Footer from '@/Layouts/Partials/Footer.vue'
import { useTheme } from 'vuetify'
import { useUserStore } from '@/stores'

const theme = useTheme()
const user = useUserStore()

watchEffect(() => {
  theme.change(user.theme)
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
