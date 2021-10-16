<template>
  <app-user-section>
    <template #title>
      Two Factor Authentication
    </template>

    <template #description>
      Add additional security to your account using two factor authentication.
    </template>

    <template #card-title>
      <h4 v-if="twoFactorEnabled">
        You have enabled two factor authentication.
      </h4>

      <h4 v-else>
        You have not enabled two factor authentication.
      </h4>
    </template>

    <template #content>
      When two factor authentication is enabled, you will be prompted for a
      secure, random token during authentication. You may retrieve this token
      from your phone's Google Authenticator application.

      <div v-if="twoFactorEnabled">
        <div v-if="qrCode">
          <p class="font-semibold">
            Two factor authentication is now enabled. Scan the following QR code
            using your phone's authenticator application.
          </p>
          <!-- eslint-disable vue/no-v-html -->
          <div
            class="mt-4"
            v-html="qrCode"
          />
          <!--eslint-enable-->
        </div>

        <div v-if="recoveryCodes.length > 0">
          <p class="font-semibold">
            Store these recovery codes in a secure password manager. They can be
            used to recover access to your account if your two factor
            authentication device is lost.
          </p>

          <div
            v-for="code in recoveryCodes"
            :key="code"
          >
            {{ code }}
          </div>
        </div>
      </div>
    </template>

    <template #actions>
      <app-confirms-password
        v-if="!twoFactorEnabled"
        @confirmed="enableTwoFactorAuthentication"
      >
        <v-btn
          :class="{ 'opacity-25': enabling }"
          :disabled="enabling"
        >
          Enable
        </v-btn>
      </app-confirms-password>

      <template v-else>
        <app-confirms-password @confirmed="regenerateRecoveryCodes">
          <v-btn
            v-if="recoveryCodes.length > 0"
            outlined
            class="mr-3"
          >
            Regenerate Recovery Codes
          </v-btn>
        </app-confirms-password>

        <app-confirms-password @confirmed="showRecoveryCodes">
          <v-btn
            v-if="recoveryCodes.length === 0"
            outlined
            class="mr-3"
          >
            Show Recovery Codes
          </v-btn>
        </app-confirms-password>

        <app-confirms-password @confirmed="disableTwoFactorAuthentication">
          <v-btn
            color="red"
            :class="{ 'opacity-25': disabling }"
            :disabled="disabling"
          >
            Disable
          </v-btn>
        </app-confirms-password>
      </template>
    </template>
  </app-user-section>
</template>

<script>
import AppUserSection from '@/Components/UserSection'
import AppConfirmsPassword from '@/Components/ConfirmsPassword'

export default {
  components: {
    AppUserSection,
    AppConfirmsPassword
  },

  data () {
    return {
      enabling: false,
      disabling: false,

      qrCode: null,
      recoveryCodes: []
    }
  },

  computed: {
    twoFactorEnabled () {
      return !this.enabling && this.$page.props.user.two_factor_enabled
    }
  },

  methods: {
    enableTwoFactorAuthentication () {
      this.enabling = true

      this.$inertia.post(
        '/user/two-factor-authentication',
        {},
        {
          preserveScroll: true,
          onSuccess: () =>
            Promise.all([this.showQrCode(), this.showRecoveryCodes()]),
          onFinish: () => (this.enabling = false)
        }
      )
    },

    showQrCode () {
      return this.axios.get('/user/two-factor-qr-code').then(response => {
        this.qrCode = response.data.svg
      })
    },

    showRecoveryCodes () {
      return this.axios
        .get('/user/two-factor-recovery-codes')
        .then(response => {
          this.recoveryCodes = response.data
        })
    },

    regenerateRecoveryCodes () {
      this.axios.post('/user/two-factor-recovery-codes').then(response => {
        this.showRecoveryCodes()
      })
    },

    disableTwoFactorAuthentication () {
      this.disabling = true

      this.$inertia.delete('/user/two-factor-authentication', {
        preserveScroll: true,
        onSuccess: () => (this.disabling = false)
      })
    }
  }
}
</script>
