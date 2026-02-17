<script setup lang="ts">
import { inject, nextTick, reactive, ref, VNodeRef } from 'vue'
import { mdiEye, mdiEyeOff } from 'mdi-js-es'
import { route as ziggyRoute } from 'ziggy-js'
import axios from 'axios'

const emit = defineEmits(['confirmed'])
withDefaults(defineProps<{
  title?: string
  content?: string
  button?: string
}>(), {
  title: 'Confirm Password',
  content: 'For your security, please confirm your password to continue.',
  button: 'Confirm',
})

const route = inject('route') as typeof ziggyRoute

const confirmingPassword = ref(false)

const form = reactive({
  password: '',
  error: '',
  processing: false,
})

const passwordInput = ref<VNodeRef | null>(null)

const startConfirmingPassword = () => {
  axios.get(route('password.confirmation')).then((response) => {
    if (response.data.confirmed) {
      emit('confirmed')
    } else {
      confirmingPassword.value = true

      setTimeout(() => passwordInput.value.focus(), 250)
    }
  })
}

const showP = ref(false)

const confirmPassword = () => {
  form.processing = true

  axios.post(route('password.confirm'), {
    password: form.password,
  }).then(() => {
    form.processing = false

    closeModal()
    nextTick().then(() => emit('confirmed'))
  }).catch((error) => {
    form.processing = false
    form.error = error.response.data.errors.password[0]
    passwordInput.value.focus()
  })
}

const closeModal = () => {
  confirmingPassword.value = false
  form.password = ''
  form.error = ''
}
</script>

<template>
  <span>
    <span @click="startConfirmingPassword">
      <slot />
    </span>

  </span>

  <v-dialog
    v-model="confirmingPassword"
    max-width="600"
  >
    <v-card>
      <v-form
        :disabled="form.processing"
        @submit.prevent="confirmPassword"
      >
        <v-card-title>
          {{ title }}
        </v-card-title>

        <v-card-text>
          <p class="mb-4">
            {{ content }}
          </p>

          <v-text-field
            ref="passwordInput"
            v-model="form.password"
            variant="outlined"
            label="Password"
            placeholder="************"
            required
            autocomplete="current-password"
            :error-messages="form.error"
            :append-inner-icon="showP ? mdiEye : mdiEyeOff"
            :type="showP ? 'text' : 'password'"
            class="mb-4"
            hide-details="auto"
            @click:append-inner="showP = !showP"
          />
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn
            :disabled="form.processing"
            @click="confirmingPassword = false"
          >
            Nevermind
          </v-btn>

          <v-btn
            :disabled="form.processing"
            color="primary"
            type="submit"
            :text="button"
          />
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>
</template>
