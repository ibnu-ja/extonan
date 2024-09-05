<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { VBtn } from 'vuetify/components'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Link from '@/Components/InertiaLink.ts'
import { mdiEye } from '@mdi/js/commonjs/mdi.js'
import { mdiEyeOff } from '@mdi/js'
import { inject, ref } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

const props = defineProps<{
  email: string
  token: string
}>()

const route = inject('route') as typeof ziggyRoute

const showP = ref(true)
const showCP = ref(true)

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('password.update'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<template>
  <Head title="Reset Password" />

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
      <v-form
        :disabled="form.processing"
        @submit.prevent="submit"
      >
        <v-card-text>
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
          <div
            class="flex align-center gap-4"
          >
            <v-spacer />
            <v-btn
              color="primary"
              type="submit"
              :disabled="form.processing"
            >
              Reset Password
            </v-btn>
          </div>
        </v-card-text>
      </v-form>
    </v-card>
  </AuthLayout>
</template>
