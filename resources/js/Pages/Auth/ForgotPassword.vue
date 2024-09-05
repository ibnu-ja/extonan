<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { VBtn } from 'vuetify/components'
import Link from '@/Components/InertiaLink.ts'
import { inject } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

defineProps<{
  status: string
}>()

const route = inject('route') as typeof ziggyRoute

const form = useForm({
  email: '',
})

const submit = () => {
  form.post(route('password.email'))
}
</script>

<template>
  <Head title="Forgot Password" />

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
          <p class="mb-4">
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset
            link that will allow you to choose a new one.
          </p>
          <v-scroll-x-transition>
            <p
              v-show="status"
              class="mb-4 text-success"
            >
              {{ status }}
            </p>
          </v-scroll-x-transition>
          <v-text-field
            id="email"
            v-model="form.email"
            :error-messages="form.errors.email"
            :disabled="form.processing"
            variant="outlined"
            type="email"
            class="mb-4"
            label="Email"
            required
            autofocus
            hide-details="auto"
            autocomplete="username"
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
              Email Password Reset Link
            </v-btn>
          </div>
        </v-card-text>
      </v-form>
    </v-card>
  </AuthLayout>
</template>
