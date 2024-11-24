<script setup lang="ts">

import { VListItem } from 'vuetify/components'
import { usePage } from '@inertiajs/vue3'
import InertiaLink from '@/Components/InertiaLink'
import { watch } from 'vue'
import { useDisplay } from 'vuetify'

const model = defineModel<boolean | undefined>()

const { name } = useDisplay()

watch(name, async () => {
  model.value = undefined
})

const page = usePage()
</script>

<template>
  <v-navigation-drawer
    v-model="model"
    temporary
    location="top"
  >
    <v-list color="primary">
      <InertiaLink
        :as="VListItem"
        exact-active
        class="mr-2"
        :href="route('home')"
      >
        Home
      </InertiaLink>

      <InertiaLink
        :as="VListItem"
        :href="route('anime.index')"
      >
        Anime
      </InertiaLink>

      <InertiaLink
        :as="VListItem"
        :href="route('mv.index')"
      >
        MV
      </InertiaLink>

      <InertiaLink
        v-if="page.props.auth.user"
        :as="VListItem"
        :href="route('dashboard')"
      >
        Dashboard
      </InertiaLink>
    </v-list>
  </v-navigation-drawer>
</template>
