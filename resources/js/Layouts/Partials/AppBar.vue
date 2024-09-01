<script lang="ts" setup>

import { inject } from 'vue'
import { mdiAccount, mdiAccountPlus, mdiApi, mdiExitRun, mdiLogin, mdiMenu } from '@mdi/js'
import { router, usePage } from '@inertiajs/vue3'
import { VBtn, VListItem } from 'vuetify/components'
import InertiaLink from '@/Components/InertiaLink.vue'
import ThemeSelector from '@/Layouts/Partials/ThemeSelector.vue'
import { BreadcrumbItem } from '@/types'
import { route as ziggyRoute } from 'ziggy-js'
import logo from '@/assets/logo/inline color.svg'

const drawer = defineModel<boolean | undefined>('drawer')

const page = usePage()

const route = inject('route') as typeof ziggyRoute

// const location = useBrowserLocation()

defineProps<{
  breadcrumbs?: BreadcrumbItem[]
}>()

const itemList = [
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
  },
]
// const tab = ref(location.value.pathname?.split('/')[1])

// const visit = (e: any) => {
//   console.log(e)
//   if (e == null) {
//     const bwang = location.value.pathname?.split('/')[1]
//
//     let currentUrl = itemList.find((item) => {
//       const bwangUrl = new URL(item.link).pathname.split('/')[1]
//       console.log(`bwang url: ${bwangUrl}`)
//       return bwangUrl == bwang
//     })
//     if (currentUrl) {
//       console.log(currentUrl)
//       tab.value = currentUrl.value
//     }
//   } else {
//     const tes = itemList.find(item => item.value == e)
//     if (tes) {
//       router.visit(tes.link)
//     }
//   }
// }

function logout() {
  router.post(route('logout'))
}
</script>

<template>
  <v-app-bar extension-height="34">
    <v-app-bar-nav-icon class="hidden-lg-and-up">
      <v-btn
        v-model="drawer"
        :icon="mdiMenu"
        @click="drawer = !drawer"
      />
    </v-app-bar-nav-icon>
    <InertiaLink
      href="/"
      class="mx-4"
    >
      <v-img
        width="200"
        :src="logo"
        alt="application logo"
      />
    </InertiaLink>
    <div class="hidden-md-and-down">
      <InertiaLink
        v-for="item in itemList"
        :key="item.link"
        :exact-active="item.exactActive"
        :as="VBtn"
        class="mr-2"
        :href="item.link"
      >
        {{ item.label }}
      </InertiaLink>
    </div>
    <!--TODO use tab, assign class text-primary to active route-->
    <!--<v-tabs>-->
    <!--  <InertiaLink-->
    <!--    v-for="item in itemList"-->
    <!--    :key="item.link"-->
    <!--    :as="VTab"-->
    <!--    :href="item.link"-->
    <!--  >-->
    <!--    {{ item.label }}-->
    <!--  </InertiaLink>-->
    <!--</v-tabs>-->
    <!--<v-tabs-->
    <!--  v-model="tab"-->
    <!--  class="hidden-md-and-down"-->
    <!--  color="primary"-->
    <!--  :mandatory="false"-->
    <!--  @update:model-value="visit"-->
    <!--&gt;-->
    <!--  <v-tab-->
    <!--    v-for="item in itemList"-->
    <!--    :key="item.value"-->
    <!--    class="mr-2"-->
    <!--    :value="item.value"-->
    <!--  >-->
    <!--    {{ item.label }}-->
    <!--  </v-tab>-->
    <!--</v-tabs>-->
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
