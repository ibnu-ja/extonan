<script setup lang="ts">
import { inject, ref, VNodeRef } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { VBtn } from 'vuetify/components'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Link from '@/Components/InertiaLink'
import { mdiEye, mdiEyeOff } from 'mdi-js-es'
import { route as ziggyRoute } from 'ziggy-js'

const form = useForm({
  password: '',
})

const route = inject('route') as typeof ziggyRoute

const showP = ref(false)

const passwordInput = ref<VNodeRef | null>(null)

const submit = () => {
  form.post(route('password.confirm'), {
    onFinish: () => {
      form.reset()
      passwordInput.value?.focus()
    },
  })
}
</script>

<template>
  <Head title="Secure Area" />

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
            This is a secure area of the application. Please confirm your password before continuing.
          </p>
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
            class="mb-4"
            hide-details="auto"
            @click:append-inner="showP = !showP"
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
              Confirm
            </v-btn>
          </div>
        </v-card-text>
      </v-form>
    </v-card>
  </AuthLayout>
</template>
