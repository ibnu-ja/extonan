<script setup lang="ts">
import { ref, VNodeRef } from 'vue'
import { useForm } from '@inertiajs/vue3'
import ActionMessage from '@/Components/ActionMessage.vue'
import FormSection from '@/Components/FormSection.vue'
import { mdiEye } from '@mdi/js/commonjs/mdi'
import { mdiEyeOff } from '@mdi/js'

const passwordInput = ref<VNodeRef | null>(null)
const currentPasswordInput = ref<VNodeRef | null>(null)
const showCurrentPassword = ref(false)
const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const updatePassword = () => {
  form.put(route('user-password.update'), {
    errorBag: 'updatePassword',
    preserveScroll: true,
    onSuccess: () => form.reset(),
    onError: () => {
      if (form.errors.password) {
        form.reset('password', 'password_confirmation')
        passwordInput.value.focus()
      }

      if (form.errors.current_password) {
        form.reset('current_password')
        currentPasswordInput.value.focus()
      }
    },
  })
}
</script>

<template>
  <FormSection @submitted="updatePassword">
    <template #title>
      Update Password
    </template>

    <template #description>
      Ensure your account is using a long, random password to stay secure.
    </template>

    <template #form>
      <v-card-text>
        <v-text-field
          ref="currentPasswordInput"
          v-model="form.current_password"
          label="Current Password"
          variant="outlined"
          autocomplete="current-password"
          :error-messages="form.errors.current_password"
          :append-icon="showCurrentPassword ? mdiEye : mdiEyeOff"
          :type="showCurrentPassword ? 'text' : 'password'"
          :disabled="form.processing"
          hide-details="auto"
          class="mb-4"
          @click:append="showCurrentPassword = !showCurrentPassword"
        />

        <v-text-field
          ref="passwordInput"
          v-model="form.password"
          variant="outlined"
          label="Password"
          required
          autocomplete="new-password"
          :error-messages="form.errors.password"
          :append-icon="showPassword ? mdiEye : mdiEyeOff"
          :type="showPassword ? 'text' : 'password'"
          :disabled="form.processing"
          hint="At least 8 characters"
          hide-details="auto"
          class="mb-4"
          @click:append="showPassword = !showPassword"
        />

        <v-text-field
          v-model="form.password_confirmation"
          variant="outlined"
          label="Confirm Password"
          required
          autocomplete="new-password"
          :error-messages="form.errors.password_confirmation"
          :append-icon="showPasswordConfirmation ? mdiEye : mdiEyeOff"
          :type="showPasswordConfirmation ? 'text' : 'password'"
          :disabled="form.processing"
          hide-details="auto"
          class="mb-4"
          @click:append="showPasswordConfirmation = !showPasswordConfirmation"
        />
      </v-card-text>
    </template>

    <template #actions>
      <ActionMessage
        :on="form.recentlySuccessful"
        class="me-3"
      >
        Saved.
      </ActionMessage>
      <v-spacer />
      <v-btn
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="form.reset()"
      >
        Clear
      </v-btn>
      <v-btn
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        type="submit"
        color="primary"
      >
        Save
      </v-btn>
    </template>
  </FormSection>
</template>
