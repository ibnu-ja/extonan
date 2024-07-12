<script setup lang="ts">
import { ref, VNodeRef } from 'vue'
import { useForm } from '@inertiajs/vue3'
import ActionMessage from '@/Components/ActionMessage.vue'
import FormSection from '@/Components/FormSection.vue'
import { mdiEye } from '@mdi/js/commonjs/mdi'
import { mdiEyeOff } from '@mdi/js'

const passwordInput = ref<VNodeRef | null>(null)
const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const setPassword = () => {
  form.post(route('user-password.set'), {
    errorBag: 'setPassword',
    preserveScroll: true,
    onSuccess: () => form.reset(),
    onError: () => {
      if (form.errors.password) {
        form.reset('password', 'password_confirmation')
        passwordInput.value.focus()
      }
    },
  })
}
</script>

<template>
  <FormSection @submitted="setPassword">
    <template #title>
      Set Password
    </template>

    <template #description>
      Ensure your account is using a long, random password to stay secure.
    </template>

    <template #form>
      <v-card-text>
        <v-text-field
          ref="passwordInput"
          v-model="form.password"
          variant="outlined"
          label="Password"
          required
          autocomplete="new-password"
          :error-messages="form.errors.password"
          :append-inner-icon="showPassword ? mdiEye : mdiEyeOff"
          :type="showPassword ? 'text' : 'password'"
          :disabled="form.processing"
          hide-details="auto"
          class="mb-4"
          @click:append-inner="showPassword = !showPassword"
        />

        <v-text-field
          v-model="form.password_confirmation"
          variant="outlined"
          label="Confirm Password"
          required
          autocomplete="new-password"
          :error-messages="form.errors.password_confirmation"
          :append-inner-icon="showPasswordConfirmation ? mdiEye : mdiEyeOff"
          :type="showPasswordConfirmation ? 'text' : 'password'"
          :disabled="form.processing"
          hide-details="auto"
          class="mb-4"
          @click:append-inner="showPasswordConfirmation = !showPasswordConfirmation"
        />
      </v-card-text>
    </template>

    <template #actions>
      <ActionMessage
        :on="form.recentlySuccessful"
        class="mr-3"
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
