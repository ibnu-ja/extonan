<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { VBtn } from 'vuetify/components'
import InertiaLink from '@/Components/InertiaLink.vue'
import { inject, ref } from 'vue'
import { mdiEye } from '@mdi/js/commonjs/mdi'
import { mdiEyeOff } from '@mdi/js'
import Socialstream from '@/Components/Socialstream.vue'
import { route as ziggyRoute } from 'ziggy-js'

const route = inject('route') as typeof ziggyRoute

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
})

const showP = ref(false)
const showCP = ref(false)

const submit = () => {
  console.log('submitting')
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}

const inertiaProps = usePage().props

</script>

<template>
  <Head title="Register" />
  <AuthLayout>
    <InertiaLink
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
    </InertiaLink>
    <v-card
      width="100%"
      max-width="400px"
    >
      <v-card-text>
        <v-form
          class="mx-auto"
          :disabled="form.processing"
          @submit.prevent="submit"
        >
          <h2 class="text-h4 mb-4">
            Welcome!
          </h2>

          <v-alert
            density="comfortable"
            type="info"
            variant="tonal"
            class="mb-4"
          >
            Already have an account?
            <InertiaLink
              :href="route('login')"
              class="text-info"
            >
              Sign in
            </InertiaLink>
          </v-alert>
          <p
            class="mb-4"
          >
            Sign up with Email
          </p>
          <v-text-field
            v-model="form.name"
            :error-messages="form.errors.name"
            placeholder="John Doe"
            required
            autofocus
            variant="outlined"
            hide-details="auto"
            label="Full Name"
            class="mb-4"
          />
          <v-text-field
            v-model="form.email"
            :error-messages="form.errors.email"
            placeholder="john@example.com"
            required
            variant="outlined"
            hide-details="auto"
            label="Email address"
            class="mb-4"
          />
          <v-text-field
            v-model="form.password"
            :error-messages="form.errors.password"
            placeholder="************"
            variant="outlined"
            label="Password"
            required
            autocomplete="new-password"
            :append-inner-icon="showP ? mdiEye : mdiEyeOff"
            :type="showP ? 'text' : 'password'"
            hide-details="auto"
            class="mb-4"
            @click:append-inner="showP = !showP"
          />
          <v-text-field
            v-model="form.password_confirmation"
            :error-messages="form.errors.password_confirmation"
            placeholder="************"
            variant="outlined"
            label="Confirm Password"
            required
            autocomplete="new-password"
            :append-inner-icon="showCP ? mdiEye : mdiEyeOff"
            :type="showCP ? 'text' : 'password'"
            hide-details="auto"
            class="mb-4"
            @click:append-inner="showCP = !showCP"
          />

          <div v-if="inertiaProps.jetstream?.hasTermsAndPrivacyPolicyFeature">
            <!-- TODO checkbox slots & error messages-->
            <v-checkbox
              v-model="form.terms"
              hide-details
              :error-messages="form.errors.terms"
              :disabled="form.processing"
            >
              <!-- TODO change target to _blank ????? -->
              <template #label>
                <span class="text-wrap">I agree to the <a
                  target="_blank"
                  href="terms-of-service"
                  v-text="'Terms of Service'"
                /> and <a
                  target="_blank"
                  href="privacy-policy"
                  v-text="'Privacy Policy'"
                />
                </span>
              </template>
            </v-checkbox>
          </div>

          <div class="d-flex mb-4">
            <v-spacer />
            <v-btn
              color="primary"
              type="submit"
              :disabled="form.processing"
            >
              Sign Up
            </v-btn>
          </div>
        </v-form>
        <Socialstream
          v-if="inertiaProps.socialstream?.show && inertiaProps.socialstream.providers.length"
          :error="inertiaProps.errors.socialstream"
          :prompt="inertiaProps.socialstream.prompt"
          :providers="inertiaProps.socialstream.providers"
        />
      </v-card-text>
    </v-card>
  </AuthLayout>
</template>
