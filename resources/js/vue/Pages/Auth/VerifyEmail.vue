<script setup lang="ts">
import { computed, inject } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { VBtn } from 'vuetify/components'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { Link as InertiaLink } from '@inertiajs/vue3'
import { route as ziggyRoute } from 'ziggy-js'

const props = defineProps<{
  status: string
}>()

const route = inject('route') as typeof ziggyRoute

const form = useForm({})

const submit = () => {
  form.post(route('verification.send'))
}

const verificationLinkSent = computed(() => props.status === 'verification-link-sent')
</script>

<template>
  <Head title="Email Verification" />
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
      max-width="480px"
    >
      <v-form
        :disabled="form.processing"
        @submit.prevent="submit"
      >
        <v-card-text>
          <p class="mb-4">
            Before continuing, could you verify your email address by clicking on the link we just emailed to you? If
            you didn't receive the email, we will gladly send you another.
          </p>
          <v-scroll-x-transition>
            <p
              v-show="verificationLinkSent"
              class="mb-4 text-success"
            >
              A new verification link has been sent to the email address you provided in your profile settings.
            </p>
          </v-scroll-x-transition>

          <div
            class="flex align-center gap-4"
          >
            <v-btn
              color="primary"
              :disabled="form.processing"
              type="submit"
            >
              Resend Verification Email
            </v-btn>
            <v-spacer />
            <InertiaLink
              :href="route('profile.show')"
              class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Edit Profile
            </InertiaLink>

            <InertiaLink
              :href="route('logout')"
              method="post"
            >
              Log Out
            </InertiaLink>
          </div>
        </v-card-text>
      </v-form>
    </v-card>
  </AuthLayout>
</template>
