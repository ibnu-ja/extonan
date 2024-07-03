<script lang="ts" setup>

import { ref } from 'vue'
import { mdiAccount, mdiAccountPlus, mdiExitRun, mdiHome, mdiLogin, mdiMagnify, mdiMenu } from '@mdi/js'
import { router, usePage } from '@inertiajs/vue3'
import { VListItem } from 'vuetify/components'
import Link from '@/Components/InertiaLink.vue'

const drawer = ref<boolean | null>(null)

const inertiaProps = usePage().props

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
      class="me-2 text-capitalize hidden-md-and-down"
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

    <v-menu location="bottom right">
      <template #activator="{ props }">
        <v-btn
          v-bind="props"
          :icon="mdiAccount"
          color="primary"
        />
      </template>
      <v-list color="primary">
        <template v-if="inertiaProps.user">
          <v-list-item>
            <v-avatar
              size="60"
            >
              <v-img
                :alt="inertiaProps.auth.user.name!"
                :src="inertiaProps.auth.user!.profile_photo_url!"
              />
            </v-avatar>
          </v-list-item>
          <v-list-item
            :title="inertiaProps.auth.user.name!"
            :subtitle="inertiaProps.auth.user.email"
          />
          <v-divider class="my-2" />
          <Link
            :as="VListItem"
            :prepend-icon="mdiAccount"
            title="Profile"
            :active="route().current('profile.show')"
            :href="route('profile.show')"
          />
          <Link
            :as="VListItem"
            :prepend-icon="mdiExitRun"
            title="Logout"
            @click="logout"
          />
        </template>
        <template v-else>
          <Link
            :as="VListItem"
            :prepend-icon="mdiLogin"
            title="Login"
            :href="route('login')"
          />
          <Link
            :as="VListItem"
            :prepend-icon="mdiAccountPlus"
            title="Register"
            :href="route('register')"
          />
        </template>
      </v-list>
    </v-menu>
    <template #extension>
      <v-breadcrumbs
        bg-color="surface-light"
        density="compact"
        class="flex-grow-1 mx-n4"
      >
        <v-container
          class="py-0"
          fluid
        >
          <v-breadcrumbs-item>
            <v-icon :icon="mdiHome" />
          </v-breadcrumbs-item>
          <template
            v-for="item in ['Foo', 'Bar', 'Fizz']"
            :key="item"
          >
            <v-breadcrumbs-divider />
            <v-breadcrumbs-item>
              {{ item }}
            </v-breadcrumbs-item>
          </template>
        </v-container>
      </v-breadcrumbs>
    </template>
  </v-app-bar>
</template>
