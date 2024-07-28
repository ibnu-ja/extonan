<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue'
import Layout from '@/Layouts/AppLayout.vue'
import SetPasswordForm from '@/Pages/Profile/Partials/SetPasswordForm.vue'
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue'
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue'
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue'
import ConnectedAccountsForm from '@/Pages/Profile/Partials/ConnectedAccountsForm.vue'
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue'
import { UserSession } from '@/types'

defineProps<{
  confirmsTwoFactorAuthentication: boolean
  sessions: UserSession[]
}>()

const page = usePage()
</script>

<template>
  <Layout title="Profile">
    <template #header>
      <h1 class="text-h4 text-md-h3">
        Profile
      </h1>
    </template>

    <v-container>
      <template v-if="page.props.jetstream.canUpdateProfileInformation">
        <UpdateProfileInformationForm :user="page.props.auth.user" />

        <v-divider class="my-8" />
      </template>

      <template v-if="page.props.jetstream.canUpdatePassword && page.props.socialstream.hasPassword">
        <UpdatePasswordForm class="mt-10 mt-sm-0" />

        <v-divider class="my-8" />
      </template>

      <template v-else>
        <SetPasswordForm class="mt-10 mt-sm-0" />

        <v-divider class="my-8" />
      </template>

      <template
        v-if="page.props.jetstream.canManageTwoFactorAuthentication && page.props.socialstream.hasPassword"
      >
        <TwoFactorAuthenticationForm
          :requires-confirmation="confirmsTwoFactorAuthentication"
          class="mt-10 mt-sm-0"
        />

        <v-divider class="my-8" />
      </template>

      <template v-if="page.props.socialstream.show">
        <ConnectedAccountsForm class="mt-10 mt-sm-0" />
      </template>

      <template v-if="page.props.socialstream.hasPassword">
        <v-divider class="my-8" />

        <LogoutOtherBrowserSessionsForm
          :sessions="sessions"
          class="mt-10 mt-sm-0"
        />
      </template>

      <template
        v-if="page.props.jetstream.hasAccountDeletionFeatures && page.props.socialstream.hasPassword"
      >
        <v-divider class="my-8" />

        <DeleteUserForm class="mt-10 mt-sm-0" />
      </template>
    </v-container>
  </Layout>
</template>
