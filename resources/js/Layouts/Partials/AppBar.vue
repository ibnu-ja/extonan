<script lang="ts" setup>

import { computed, inject, ref } from 'vue'
import { mdiAccount, mdiAccountPlus, mdiApi, mdiExitRun, mdiLogin, mdiMenu } from '@mdi/js'
import { router, usePage } from '@inertiajs/vue3'
import { VBtn, VListItem, VTab } from 'vuetify/components'
import InertiaLink from '@/Components/InertiaLink.ts'
import ThemeSelector from '@/Layouts/Partials/ThemeSelector.vue'
import { BreadcrumbItem } from '@/types'
import { route as ziggyRoute } from 'ziggy-js'
import logoIcon from '@/assets/logo/logo color.svg'
import logo from '@/assets/logo/inline color.svg'
import { useBrowserLocation } from '@vueuse/core'
import { useDisplay } from 'vuetify'

const drawer = defineModel<boolean | undefined>('drawer')

const page = usePage()

const route = inject('route') as typeof ziggyRoute

const location = useBrowserLocation()

defineProps<{
  breadcrumbs?: BreadcrumbItem[]
}>()

const itemList = computed(() => [
  {
    value: '',
    link: route('home'),
    label: 'Home',
    exactActive: true,
  },
  {
    value: 'anime',
    link: route('anime.index'),
    label: 'Anime',
    exactActive: false,
  },
  {
    value: 'dashboard',
    link: route('dashboard'),
    label: 'Dashboard',
    exactActive: false,
    hide: !page.props.auth.user,
  },
])
const tab = ref(location.value.pathname?.split('/')[1])

const visit = (e: unknown) => {
  if (e !== undefined) {
    const tes = itemList.value.find(item => item.value === e)
    if (tes) {
      router.visit(tes.link)
    }
    return
  }

  const bwang = location.value.pathname?.split('/')[1]

  const currentUrl = itemList.value.find(item => new URL(item.link).pathname.split('/')[1] === bwang)

  if (currentUrl) {
    tab.value = currentUrl.value
  }
}

// const tab = ref()

const { lgAndUp } = useDisplay()

function logout() {
  router.post(route('logout'))
}

</script>

<template>
  <v-app-bar extension-height="34">
    <v-app-bar-nav-icon v-if="!lgAndUp">
      <v-btn
        v-model="drawer"
        :icon="mdiMenu"
        @click="drawer = !drawer"
      />
    </v-app-bar-nav-icon>

    <!--<UseBrowserLocation v-slot="{ location }">-->
    <!--  Browser Location: {{ location }}-->
    <!--</UseBrowserLocation>-->
    <!--<LogoIcon style="width: 50px" />-->
    <InertiaLink
      href="/"
    >
      <picture>
        <source
          :srcset="logo"
          media="(min-width: 960px)"
        >
        <img
          class="w-[50px] md:w-[200px] md:mx-4"
          style="vertical-align: middle"
          :src="logoIcon"
          alt="application logo"
        >
      </picture>
    </InertiaLink>
    <v-tabs
      v-if="lgAndUp"
      v-model="tab"
      color="primary"
      :mandatory="false"
      @update:model-value="visit"
    >
      <template
        v-for="item in itemList"
        :key="item.value"
      >
        <v-tab
          v-if="!item!.hide"
          class="mr-2"
          :value="item.value"
        >
          {{ item.label }}
        </v-tab>
      </template>
    </v-tabs>
    <v-spacer />
    <ThemeSelector />
    <!--user menu-->
    <v-menu location="bottom right">
      <template #activator="{ props }">
        <v-btn
          v-bind="props"
          icon
        >
          <v-avatar v-if="page.props.auth.user">
            <v-img
              :alt="page.props.auth.user.name!"
              :src="page.props.auth.user!.profile_photo_url!"
            />
          </v-avatar>
          <v-icon
            v-else
            :icon="mdiAccount"
          />
        </v-btn>
      </template>
      <v-list color="primary">
        <template v-if="page.props.auth.user">
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
        <template v-else-if="page.props.canLogin || page.props.canRegister">
          <InertiaLink
            v-if="page.props.canLogin"
            :as="VListItem"
            :prepend-icon="mdiLogin"
            title="Login"
            :href="route('login')"
          />
          <InertiaLink
            v-if="page.props.canRegister"
            :as="VListItem"
            :prepend-icon="mdiAccountPlus"
            title="Register"
            :href="route('register')"
          />
        </template>
      </v-list>
    </v-menu>
    <!--<template-->
    <!--  v-if="breadcrumbs"-->
    <!--  #extension-->
    <!--&gt;-->
    <!--  <v-breadcrumbs-->
    <!--    :items="breadcrumbs"-->
    <!--    bg-color="surface-light"-->
    <!--    density="compact"-->
    <!--    class="flex-grow-1"-->
    <!--  >-->
    <!--    <template #item="{item}">-->
    <!--      <InertiaLink-->
    <!--        :as="VBreadcrumbsItem"-->
    <!--        :disabled="item.disabled"-->
    <!--        :exact-active="item.exact"-->
    <!--        :href="item.href"-->
    <!--      >-->
    <!--        {{ item.title }}-->
    <!--      </InertiaLink>-->
    <!--    </template>-->
    <!--  </v-breadcrumbs>-->
    <!--</template>-->
  </v-app-bar>
</template>
