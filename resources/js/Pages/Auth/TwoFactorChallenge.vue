<script setup lang="ts">
import { inject, nextTick, ref, VNodeRef } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { VBtn } from 'vuetify/components'
import Link from '@/Components/InertiaLink'
import { route as ziggyRoute } from 'ziggy-js'

const recovery = ref(false)

const route = inject('route') as typeof ziggyRoute

const form = useForm({
  code: '',
  recovery_code: '',
})

const recoveryCodeInput = ref<VNodeRef | null>(null)
const codeInput = ref<VNodeRef | null>(null)

const toggleRecovery = async () => {
  recovery.value = !recovery.value

  await nextTick()
  form.reset()

  if (recovery.value) {
    recoveryCodeInput.value.focus()
    form.code = ''
  } else {
    codeInput.value.focus()
    form.recovery_code = ''
  }
}

const submit = () => {
  form.post(route('two-factor.login'))
}
</script>

<template>
  <Head title="Two-factor Confirmation" />
  <AuthLayout>
    <Link
      :as="VBtn"
      href="/"
      flat
      icon
      size="100"
      class="mb-4"
    >
      <v-avatar
        color="grey darken-1"
        size="100"
      />
    </Link>

    <v-card
      width="100%"
      max-width="400px"
    >
      <v-form @submit.prevent="submit">
        <v-card-text>
          <p class="mb-4">
            <template v-if="! recovery">
              Please confirm access to your account by entering the authentication code provided by your authenticator
              application.
            </template>

            <template v-else>
              Please confirm access to your account by entering one of your emergency recovery codes.
            </template>
          </p>
          <v-text-field
            v-if="!recovery"
            ref="codeInput"
            v-model="form.code"
            :error-messages="form.errors.code"
            type="text"
            inputmode="numeric"
            class="mb-4"
            autofocus
            autocomplete="one-time-code"
            hide-details="auto"
            variant="outlined"
            label="Code"
            required
          />
          <!--TODO use OTP component-->
          <!--<div-->
          <!--  v-if="!recovery"-->
          <!--  class="mb-4"-->
          <!--&gt;-->
          <!--  <v-otp-input-->
          <!--    v-model="form.code"-->
          <!--    class="p-0"-->
          <!--    :error="form.errors.code != null"-->
          <!--  />-->
          <!--  <div-->
          <!--    v-if="form.errors.code != null"-->
          <!--    class="v-input__details text-error"-->
          <!--  >-->
          <!--    <div-->
          <!--      id="input-41-messages"-->
          <!--      class="v-messages"-->
          <!--      role="alert"-->
          <!--      aria-live="polite"-->
          <!--    >-->
          <!--      <div class="v-messages__message">-->
          <!--        The provided two factor recovery code was invalid.-->
          <!--      </div>-->
          <!--    </div>-->
          <!--  </div>-->
          <!--</div>-->
          <v-text-field
            v-else
            ref="recoveryCodeInput"
            v-model="form.recovery_code"
            :error-messages="form.errors.recovery_code"
            type="text"
            inputmode="numeric"
            class="mb-4"
            autofocus
            autocomplete="one-time-code"
            hide-details="auto"
            variant="outlined"
            label="Recovery Code"
            required
          />

          <div
            class="flex align-center gap-4"
          >
            <v-spacer />
            <a
              href=""
              type="button"
              @click.prevent="toggleRecovery"
            >
              <template v-if="! recovery">
                Use a recovery code
              </template>

              <template v-else>
                Use an authentication code
              </template>
            </a>
            <v-btn
              color="primary"
              type="submit"
              :disabled="form.processing"
            >
              Sign In
            </v-btn>
          </div>
        </v-card-text>
      </v-form>
    </v-card>
  </AuthLayout>
</template>
