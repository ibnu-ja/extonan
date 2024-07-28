<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { computed, watchEffect } from 'vue'
import { useTheme } from 'vuetify'

import { useUserStore } from '@/stores/userStore'
import { usePreferredDark } from '@vueuse/core'
import Banner from '@/Components/Banner.vue'

const theme = useTheme()
const user = useUserStore()
const systemTheme = computed(() => usePreferredDark().value ? 'dark' : 'light')

watchEffect(() => {
  theme.global.name.value = (
    user.theme === 'system' ? systemTheme.value : user.theme
  )
})
defineProps({
  title: {
    type: String,
    default: () => 'Laravel',
  },
})
</script>

<template>
  <Head>
    <title inertia>
      {{ title }}
    </title>

    <!--      :content="colorScheme"-->
    <meta name="color-scheme">
  </Head>
  <v-app>
    <Banner />
    <v-main>
      <v-container class="fill-height d-flex flex-column justify-center align-center">
        <slot />
      </v-container>
    </v-main>
  </v-app>
</template>
