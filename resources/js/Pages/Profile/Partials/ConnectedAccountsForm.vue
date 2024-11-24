<script lang="ts" setup>
import { inject, ref, VNodeRef } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import ActionSection from '@/Components/ActionSection.vue'
import ConnectedAccount from '@/Components/ConnectedAccount.vue'
import { SocialstreamProviders } from '@/types'
import { route as ziggyRoute } from 'ziggy-js'

const accountId = ref<number | null>(null)
const confirmingRemoveAccount = ref(false)
const passwordInput = ref<VNodeRef | null>(null)

const page = usePage()

const route = inject('route') as typeof ziggyRoute

const form = useForm({
  password: '',
})

const getAccountForProvider = (provider: SocialstreamProviders) => page.props.socialstream.connectedAccounts
  .filter(account => account.provider === provider.id)
  .shift()

function setProfilePhoto(id: number | undefined) {
  form.put(route('user-profile-photo.set', { id }), {
    preserveScroll: true,
  })
}

const confirmRemoveAccount = (id: number | undefined) => {
  accountId.value = id!

  confirmingRemoveAccount.value = true

  setTimeout(() => passwordInput.value?.focus(), 250)
}

const removeAccount = () => {
  form.delete(route('connected-accounts.destroy', { id: accountId.value! }), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value?.focus(),
    onFinish: () => form.reset(),
  })
}

const closeModal = () => {
  confirmingRemoveAccount.value = false

  form.reset()
}

</script>

<template>
  <ActionSection>
    <template #title>
      Connected Accounts
    </template>

    <template #description>
      Connect your social media accounts to enable Sign In with OAuth.
    </template>

    <template #content>
      <v-card-text>
        <v-alert
          type="info"
          variant="tonal"
        >
          If you feel any of your connected accounts have been compromised, you should disconnect them
          immediately and change your password.
        </v-alert>
      </v-card-text>
      <ConnectedAccount
        v-for="(provider) in page.props.socialstream.providers"
        :key="provider.id"
        :provider="provider"
        :created-at="getAccountForProvider(provider)?.created_at!"
      >
        <template #action>
          <template v-if="getAccountForProvider(provider)">
            <v-btn
              v-if="
                page.props.jetstream?.managesProfilePhotos &&
                  getAccountForProvider(provider)?.avatar_path
              "
              @click="setProfilePhoto(getAccountForProvider(provider)?.id)"
            >
              Use Avatar as Profile Photo
            </v-btn>

            <v-btn
              v-if="
                page.props.socialstream!.connectedAccounts.length > 1 ||
                  page.props.socialstream!.hasPassword
              "
              color="error"
              @click="confirmRemoveAccount(getAccountForProvider(provider)?.id)"
            >
              Remove
            </v-btn>
          </template>

          <template v-else>
            <v-btn
              :href="route('oauth.redirect', { provider })"
              outlined
              color="primary"
            >
              Connect
            </v-btn>
          </template>
        </template>
      </ConnectedAccount>

      <!--Confirmation Modal-->
      <v-dialog
        v-model="confirmingRemoveAccount"
        max-width="600"
      >
        <v-card>
          <v-card-title>
            Are you sure you want to remove this account?
          </v-card-title>

          <v-card-text>
            <p class="mb-4">
              Please enter your password to confirm you would like to remove this account.
            </p>

            <v-text-field
              :ref="passwordInput"
              v-model="form.password"
              variant="outlined"
              hide-details="auto"
              placeholder="Password"
              autocomplete="current-password"
              :error-messages="form.errors.password"
              class="mb-4"
              @keyup.enter="removeAccount"
            />
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn
              color="primary"
              @click="closeModal"
            >
              Cancel
            </v-btn>

            <v-btn
              color="error"
              :disabled="form.processing"
              @click="removeAccount"
            >
              Remove Account
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </template>
  </ActionSection>
</template>
