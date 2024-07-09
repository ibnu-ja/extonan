<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3'
import Socialstream from '@/Components/Socialstream.vue'

import { Route, Socialstream as SocialstreamType } from '@/types'

import AuthLayout from '@/Layouts/AuthLayout.vue'
import Link from '@/Components/InertiaLink.vue'
import { VBtn } from 'vuetify/components'
import { mdiEye } from '@mdi/js/commonjs/mdi'
import { mdiEyeOff } from '@mdi/js'
import { inject, ref } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'
import { ziggySymbol } from '@/symbols/ziggySymbol'

// defineProps({
//     canResetPassword: Boolean,
//     status: String,
// });

const inertiaProps = usePage().props

defineProps<{
  canResetPassword: boolean
  status: string
  socialstream: SocialstreamType
}>()

// const route = inject(ziggySymbol)!

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const showP = ref(false)

const submit = () => {
  form.transform(data => ({
    ...data,
    remember: form.remember ? 'on' : '',
  })).post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}

</script>

<template>
  <Head title="Log in" />
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
      <v-card-text>
        <v-form
          class="mx-auto"
          :disabled="form.processing"
          @submit.prevent="submit"
        >
          <h2 class="text-h4 mb-4">
            Welcome Back!
          </h2>
          <v-alert
            density="comfortable"
            type="info"
            variant="tonal"
            class="mb-4"
          >
            Don't have an account?
            <Link
              :href="route('register')"
              class="text-info"
            >
              Sign up
            </Link>
          </v-alert>
          <p
            v-if="status"
            class="text-success mb-4"
          >
            {{ status }}
          </p>
          <p
            v-else
            class="mb-4"
          >
            Please sign-in to your account
          </p><v-text-field
            v-model="form.email"
            variant="outlined"
            label="Email Address"
            :error-messages="form.errors.email"
            placeholder="john@example.com"
            required
            autofocus
          />
          <v-text-field
            v-model="form.password"
            variant="outlined"
            label="Password"
            placeholder="************"
            required
            autocomplete="current-password"
            :error-messages="form.errors.password"
            :append-inner-icon="showP ? mdiEye : mdiEyeOff"
            :type="showP ? 'text' : 'password'"
            hint="At least 8 characters"
            @click:append-inner="showP = !showP"
          />
          <v-checkbox
            v-model="form.remember"
            hide-details
            label="Remember me"
          />
          <div
            class="d-flex align-center"
          >
            <Link
              v-if="canResetPassword"
              :href="route('password.request')"
            >
              Forgot your password?
            </Link>
            <v-spacer />
            <v-btn
              color="primary"
              type="submit"
              :disabled="form.processing"
            >
              Sign In
            </v-btn>
          </div>
        </v-form>
        <Socialstream
          v-if="socialstream?.show && socialstream.providers.length"
          :error="inertiaProps.errors.socialstream"
          :prompt="socialstream.prompt"
          :providers="socialstream.providers"
        />
      </v-card-text>
    </v-card>
    <!--      <AuthenticationCard>-->
    <!--          <template #logo>-->
    <!--              <AuthenticationCardLogo/>-->
    <!--          </template>-->

    <!--          <form @submit.prevent="submit">-->
    <!--              <div>-->
    <!--                  <InputLabel for="email" value="Email"/>-->
    <!--                  <TextInput-->
    <!--                      id="email"-->
    <!--                      v-model="form.email"-->
    <!--                      autofocus-->
    <!--                      class="mt-1 block w-full"-->
    <!--                      required-->
    <!--                      type="email"-->
    <!--                  />-->
    <!--                  <InputError :message="form.errors.email" class="mt-2"/>-->
    <!--              </div>-->

    <!--              <div class="mt-4">-->
    <!--                  <InputLabel for="password" value="Password"/>-->
    <!--                  <TextInput-->
    <!--                      id="password"-->
    <!--                      v-model="form.password"-->
    <!--                      autocomplete="current-password"-->
    <!--                      class="mt-1 block w-full"-->
    <!--                      required-->
    <!--                      type="password"-->
    <!--                  />-->
    <!--                  <InputError :message="form.errors.password" class="mt-2"/>-->
    <!--              </div>-->

    <!--              <div class="block mt-4">-->
    <!--                  <label class="flex items-center">-->
    <!--                      <Checkbox v-model:checked="form.remember" name="remember"/>-->
    <!--                      <span class="ml-2 text-sm text-gray-600">Remember me</span>-->
    <!--                  </label>-->
    <!--              </div>-->

    <!--              <div class="flex items-center justify-end mt-4">-->
    <!--                  <Link v-if="canResetPassword" :href="route('password.request')"-->
    <!--                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">-->
    <!--                      Forgot your password?-->
    <!--                  </Link>-->

    <!--                  <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="ml-4">-->
    <!--                      Log in-->
    <!--                  </PrimaryButton>-->
    <!--              </div>-->
    <!--          </form>-->

    <!--      </AuthenticationCard>-->
  </AuthLayout>
</template>
