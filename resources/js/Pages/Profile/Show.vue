<script lang="ts">

import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  layout: AppLayout,
}
</script>

<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3'
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue'
import SetPasswordForm from '@/Pages/Profile/Partials/SetPasswordForm.vue'
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue'
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue'
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue'
import ConnectedAccountsForm from '@/Pages/Profile/Partials/ConnectedAccountsForm.vue'
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue'
import { UserSession } from '@/types'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'

defineProps<{
  confirmsTwoFactorAuthentication: boolean
  sessions: UserSession[]
}>()

const page = usePage()
</script>

<template>
  <Head>
    Profile
  </Head>
  <PageHeader title="Profile" />

  <v-container>
    <template v-if="page.props.jetstream.canUpdateProfileInformation">
      <UpdateProfileInformationForm :user="page.props.auth.user!" />

      <v-divider class="my-8" />
    </template>

    <template v-if="page.props.jetstream.canUpdatePassword && page.props.socialstream.hasPassword">
      <UpdatePasswordForm class="mt-10 sm:mt-0" />

      <v-divider class="my-8" />
    </template>

    <template v-else>
      <SetPasswordForm class="mt-10 sm:mt-0" />

      <v-divider class="my-8" />
    </template>

    <template
      v-if="page.props.jetstream.canManageTwoFactorAuthentication && page.props.socialstream.hasPassword"
    >
      <TwoFactorAuthenticationForm
        :requires-confirmation="confirmsTwoFactorAuthentication"
        class="mt-10 sm:mt-0"
      />

      <v-divider class="my-8" />
    </template>

    <template v-if="page.props.socialstream.show">
      <ConnectedAccountsForm class="mt-10 sm:mt-0" />
    </template>

    <template v-if="page.props.socialstream.hasPassword">
      <v-divider class="my-8" />

      <LogoutOtherBrowserSessionsForm
        :sessions="sessions"
        class="mt-10 sm:mt-0"
      />
    </template>

    <template
      v-if="page.props.jetstream.hasAccountDeletionFeatures && page.props.socialstream.hasPassword"
    >
      <v-divider class="my-8" />

      <DeleteUserForm class="mt-10 sm:mt-0" />
    </template>
  </v-container>
</template>
