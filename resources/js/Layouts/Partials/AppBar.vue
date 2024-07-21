<script lang="ts" setup>

import { ref } from 'vue'
import { mdiAccount, mdiAccountPlus, mdiApi, mdiExitRun, mdiHome, mdiLogin, mdiMagnify, mdiMenu } from '@mdi/js'
import { router, usePage } from '@inertiajs/vue3'
import { VBreadcrumbsItem, VListItem } from 'vuetify/components'
import InertiaLink from '@/Components/InertiaLink.vue'
import ThemeSelector from '@/Layouts/Partials/ThemeSelector.vue'
import { BreadcrumbItem } from '@/types'

const drawer = ref<boolean | null>(null)

const page = usePage()

defineProps<{
  breadcrumbs?: BreadcrumbItem[]
}>()

function logout() {
  router.post(route('logout'))
}
</script>

<template>
  <v-app-bar extension-height="34">
    <v-btn
      v-model="drawer"
      class="hidden-lg-and-up"
      :icon="mdiMenu"
      @click="drawer = !drawer"
    />
    <v-app-bar-nav-icon>
      <v-icon :icon="mdiHome" />
    </v-app-bar-nav-icon>

    <v-btn
      v-for="i in 5"
      :key="i"
      class="me-2 hidden-md-and-down"
    >
      menu {{ i }}
    </v-btn>
    <v-spacer />
    <v-text-field
      class="mr-3"
      disabled
      density="compact"
      variant="outlined"
      :prepend-inner-icon="mdiMagnify"
      hide-details
      label="Search"
    />

    <ThemeSelector />

    <v-menu location="bottom right">
      <template #activator="{ props }">
        <v-btn
          v-bind="props"
          :icon="mdiAccount"
          color="primary"
        />
      </template>
      <v-list color="primary">
        <template v-if="page.props.auth.user">
          <v-list-item>
            <v-avatar
              size="60"
            >
              <v-img
                :alt="page.props.auth.user.name!"
                :src="page.props.auth.user!.profile_photo_url!"
              />
            </v-avatar>
          </v-list-item>
          <v-list-item
            :title="page.props.auth.user.name!"
            :subtitle="page.props.auth.user.email"
          />
          <v-list-subheader>Manage account</v-list-subheader>
          <InertiaLink
            :as="VListItem"
            :prepend-icon="mdiAccount"
            title="Profile"
            :href="route('profile.show')"
          />
          <InertiaLink
            v-if="page.props.jetstream.hasApiFeatures"
            :as="VListItem"
            :prepend-icon="mdiApi"
            title="API Tokens"
            :href="route('api-tokens.index')"
          />
          <v-divider class="my-2" />
          <InertiaLink
            :as="VListItem"
            :prepend-icon="mdiExitRun"
            title="Logout"
            @click="logout"
          />
        </template>
        <template v-else>
          <InertiaLink
            :as="VListItem"
            :prepend-icon="mdiLogin"
            title="Login"
            :href="route('login')"
          />
          <InertiaLink
            :as="VListItem"
            :prepend-icon="mdiAccountPlus"
            title="Register"
            :href="route('register')"
          />
        </template>
      </v-list>
    </v-menu>
    <template
      v-if="breadcrumbs"
      #extension
    >
      <v-breadcrumbs
        :items="breadcrumbs"
        bg-color="surface-light"
        density="compact"
        class="flex-grow-1"
      >
        <template #item="{item}">
          <InertiaLink
            :as="VBreadcrumbsItem"
            :disabled="item.disabled"
            :exact-active="item.exact"
            :href="item.href"
          >
            {{ item.title }}
          </InertiaLink>
        </template>
      </v-breadcrumbs>
    </template>
  </v-app-bar>
</template>
