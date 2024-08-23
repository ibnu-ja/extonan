<script setup lang="ts">
import { computed, inject, ref, watch } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import ActionSection from '@/Components/ActionSection.vue'
import ConfirmsPassword from '@/Components/ConfirmsPassword.vue'
import { mdiContentCopy } from '@mdi/js'
import { UseClipboard } from '@vueuse/components'
import { route as ziggyRoute } from 'ziggy-js'
import axios from 'axios'

const route = inject('route') as typeof ziggyRoute

const props = defineProps({
  requiresConfirmation: Boolean,
})

const page = usePage()
const enabling = ref(false)
const confirming = ref(false)
const disabling = ref(false)
const qrCode = ref(null)
const setupKey = ref(null)
const recoveryCodes = ref<string[]>([])

const confirmationForm = useForm({
  code: '',
})

const twoFactorEnabled = computed(
  () => !enabling.value && page.props.auth.user?.two_factor_enabled,
)

watch(twoFactorEnabled, () => {
  if (!twoFactorEnabled.value) {
    confirmationForm.reset()
    confirmationForm.clearErrors()
  }
})

const enableTwoFactorAuthentication = () => {
  enabling.value = true

  router.post(route('two-factor.enable'), {}, {
    preserveScroll: true,
    onSuccess: () => Promise.all([
      showQrCode(),
      showSetupKey(),
      showRecoveryCodes(),
    ]),
    onFinish: () => {
      enabling.value = false
      confirming.value = props.requiresConfirmation
    },
  })
}

const showQrCode = async () => {
  let response = await axios.get(route('two-factor.qr-code'))
  qrCode.value = response.data.svg
}

const showSetupKey = async () => {
  let response = await axios.get(route('two-factor.secret-key'))
  setupKey.value = response.data.secretKey
}

const showRecoveryCodes = async () => {
  let response = await axios.get(route('two-factor.recovery-codes'))
  recoveryCodes.value = response.data
}

const confirmTwoFactorAuthentication = () => {
  confirmationForm.post(route('two-factor.confirm'), {
    errorBag: 'confirmTwoFactorAuthentication',
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      confirming.value = false
      qrCode.value = null
      setupKey.value = null
    },
  })
}

const regenerateRecoveryCodes = () => {
  axios
    .post(route('two-factor.recovery-codes'))
    .then(() => showRecoveryCodes())
}

const disableTwoFactorAuthentication = () => {
  disabling.value = true

  router.delete(route('two-factor.disable'), {
    preserveScroll: true,
    onSuccess: () => {
      disabling.value = false
      confirming.value = false
    },
  })
}
</script>

<template>
  <ActionSection>
    <template #title>
      Two Factor Authentication
    </template>

    <template #description>
      Add additional security to your account using two factor authentication.
    </template>

    <template #content>
      <v-card-title>
        <h3
          v-if="twoFactorEnabled && ! confirming"
          class="text-h6"
        >
          You have enabled two factor authentication.
        </h3>
        <h3
          v-else-if="twoFactorEnabled && confirming"
          class="text-h6"
        >
          Finish enabling two factor authentication.
        </h3>

        <h3
          v-else
          class="text-h6"
        >
          You have not enabled two factor authentication.
        </h3>
      </v-card-title>
      <v-card-text>
        <p>
          When two factor authentication is enabled, you will be prompted for a secure, random token during
          authentication. You may retrieve this token from your phone's Google Authenticator application.
        </p>

        <div v-if="twoFactorEnabled">
          <div v-if="qrCode">
            <div class="mt-4">
              <p
                v-if="confirming"
                class="text-body-2 font-weight-medium"
              >
                To finish enabling two factor authentication, scan the following QR code using your phone's
                authenticator application or enter the setup key and provide the generated OTP code.
              </p>

              <p v-else>
                Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator
                application or enter the setup key.
              </p>
            </div>

            <!--eslint-disable vue/no-v-html-->
            <div
              class="d-inline-block rounded-lg mt-4 p-4 bg-white"
              v-html="qrCode"
            />
            <!--eslint-enable-->

            <p
              v-if="setupKey"
              class="font-weight-medium mt-4"
            >
              Setup Key: <span v-text="setupKey" />
            </p>

            <v-text-field
              v-if="confirming"
              v-model="confirmationForm.code"
              max-width="300"
              class="mt-4"
              hide-details="auto"
              type="text"
              inputmode="numeric"
              :error-messages="confirmationForm.errors.code"
              name="code"
              label="Code"
              required
              autofocus
              autocomplete="one-time-code"
              variant="outlined"
              @keyup.enter="confirmTwoFactorAuthentication"
            />
          </div>

          <div v-if="recoveryCodes.length > 0 && ! confirming">
            <div class="mt-4">
              <p class="font-weight-medium">
                Store these recovery codes in a secure password manager. They can be used to recover access to your
                account if your two factor authentication device is lost.
              </p>
            </div>
            <template v-if="recoveryCodes.length > 0 && !confirming">
              <v-sheet
                class="mt-4 p-4"
                rounded="lg"
                style="position: relative; background-color: rgba(0, 0, 0, 0.1);"
              >
                <UseClipboard
                  v-slot="{ copy, copied }"
                  :source="recoveryCodes.join('\n')"
                >
                  <v-tooltip
                    location="left"
                  >
                    <template #activator="slot">
                      <v-btn
                        v-bind="slot.props"
                        variant="plain"
                        style="position: absolute; top: 4px; right: 4px"
                        :icon="mdiContentCopy"
                        @click="copy()"
                      />
                    </template>

                    <span v-text="copied ? 'Copied!': 'Copy Content'" />
                  </v-tooltip>
                </UseClipboard>
                <pre v-text="recoveryCodes.join('\n')" />
              </v-sheet>
            </template>
          </div>
        </div>
      </v-card-text>
      <v-card-actions>
        <template v-if="! twoFactorEnabled">
          <ConfirmsPassword @confirmed="enableTwoFactorAuthentication">
            <v-btn
              color="primary"
              :disabled="enabling"
            >
              Enable
            </v-btn>
          </ConfirmsPassword>
        </template>

        <template v-else>
          <ConfirmsPassword @confirmed="confirmTwoFactorAuthentication">
            <v-btn
              v-if="confirming"
              color="primary"
              :disabled="enabling"
            >
              Confirm
            </v-btn>
          </ConfirmsPassword>

          <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
            <v-btn
              v-if="recoveryCodes.length > 0 && ! confirming"
              color="secondary"
            >
              Regenerate Recovery Codes
            </v-btn>
          </ConfirmsPassword>

          <ConfirmsPassword @confirmed="showRecoveryCodes">
            <v-btn
              v-if="recoveryCodes.length === 0 && ! confirming"
              color="secondary"
            >
              Show Recovery Codes
            </v-btn>
          </ConfirmsPassword>

          <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
            <v-btn
              v-if="confirming"
              color="secondary"
              :disabled="disabling"
            >
              Cancel
            </v-btn>
          </ConfirmsPassword>

          <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
            <v-btn
              v-if="! confirming"
              color="error"
              :disabled="disabling"
            >
              Disable
            </v-btn>
          </ConfirmsPassword>
        </template>
      </v-card-actions>
    </template>
  </ActionSection>
</template>
