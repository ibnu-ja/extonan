<template>
  <app-user-section>
    <template #title>
      Connected Accounts
    </template>

    <template #description>
      Manage and remove your connected accounts.
    </template>

    <template #card-title>
      <h4 v-if="$page.props.socialstream.connectedAccounts.length === 0">
        You have no connected accounts.
      </h4>
      <h4
        v-else
        class="text-lg font-medium text-gray-900"
      >
        Your connected accounts.
      </h4>
    </template>

    <template #content>
      <p>
        You are free to connect any social accounts to your profile and may
        remove any connected accounts at any time. If you feel any of your
        connected accounts have been compromised, you should disconnect them
        immediately and change your password.
      </p>

      <connected-account
        v-for="provider in $page.props.socialstream.providers"
        :key="provider"
        :provider="provider"
        :created-at="
          hasAccountForProvider(provider)
            ? getAccountForProvider(provider).created_at
            : null
        "
      >
        <template #action>
          <template v-if="hasAccountForProvider(provider)">
            <v-btn
              v-if="
                $page.props.jetstream.managesProfilePhotos &&
                  getAccountForProvider(provider).avatar_path
              "
              class="cursor-pointer ml-6 text-sm text-gray-500 focus:outline-none"
              @click="setProfilePhoto(getAccountForProvider(provider).id)"
            >
              Use Avatar as Profile Photo
            </v-btn>

            <v-btn
              v-if="
                $page.props.socialstream.connectedAccounts.length > 1 ||
                  $page.props.socialstream.hasPassword
              "
              color="secondary"
              @click="confirmRemove(getAccountForProvider(provider).id)"
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
      </connected-account>

      <v-dialog
        v-model="confirmingRemove"
        max-width="672"
      >
        <v-card>
          <v-card-title class="headline">
            Remove Connected Account
          </v-card-title>

          <v-card-text>
            Please confirm your removal of this account - this action cannot be
            undone.
          </v-card-text>

          <v-card-actions class="px-6 py-4">
            <v-spacer />
            <v-btn
              color="secondary"
              @click.native="closeModal"
            >
              Nevermind
            </v-btn>

            <v-btn
              outlined
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="removeConnectedAccount(accountId)"
            >
              Remove Connected Account
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </template>
  </app-user-section>
</template>

<script>
import AppUserSection from '@/Components/UserSection'
import ConnectedAccount from '@/Socialstream/ConnectedAccount'

export default {
  components: {
    AppUserSection,
    ConnectedAccount
  },

  data () {
    return {
      confirmingRemove: false,
      accountId: null,

      form: this.$inertia.form(
        {
          _method: 'DELETE'
        },
        {
          bag: 'removeConnectedAccount'
        }
      )
    }
  },

  methods: {
    closeModal () {
      this.confirmingRemove = false

      this.form.reset()
    },
    confirmRemove (id) {
      this.form.password = ''

      this.accountId = id

      this.confirmingRemove = true
    },

    hasAccountForProvider (provider) {
      return (
        this.$page.props.socialstream.connectedAccounts.filter(
          account => account.provider === provider
        ).length > 0
      )
    },

    getAccountForProvider (provider) {
      if (this.hasAccountForProvider(provider)) {
        return this.$page.props.socialstream.connectedAccounts
          .filter(account => account.provider === provider)
          .shift()
      }

      return null
    },

    setProfilePhoto (id) {
      this.form.put(this.route('user-profile-photo.set', { id }), {
        preserveScroll: true
      })
    },

    removeConnectedAccount (id) {
      this.form.post(this.route('connected-accounts.destroy', { id }), {
        preserveScroll: true,
        onSuccess: () => (this.confirmingRemove = false)
      })
    }
  }
}
</script>
