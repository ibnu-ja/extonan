<script setup lang="ts">
import { inject, ref, VNodeRef } from 'vue'
import { useForm } from '@inertiajs/vue3'
import ActionSection from '@/Components/ActionSection.vue'
import { mdiEye, mdiEyeOff } from 'mdi-js-es'
import { route as ziggyRoute } from 'ziggy-js'

const route = inject('route') as typeof ziggyRoute

const confirmingUserDeletion = ref(false)
const passwordInput = ref<VNodeRef | null>(null)

const form = useForm({
  password: '',
})

const showP = ref(false)

const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true

  setTimeout(() => passwordInput.value?.focus(), 250)
}

const deleteUser = () => {
  form.delete(route('current-user.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value?.focus(),
    onFinish: () => form.reset(),
  })
}

const closeModal = () => {
  confirmingUserDeletion.value = false

  form.reset()
}
</script>

<template>
  <ActionSection>
    <template #title>
      Delete Account
    </template>

    <template #description>
      Permanently delete your account.
    </template>

    <template #content>
      <v-card-text>
        <p>
          Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your
          account, please download any data or information that you wish to retain.
        </p>
      </v-card-text>

      <v-card-actions>
        <v-btn
          color="error"
          @click="confirmUserDeletion"
        >
          Delete Account
        </v-btn>
      </v-card-actions>

      <!-- Delete Account Confirmation Modal -->
      <v-dialog
        v-model="confirmingUserDeletion"
        max-width="600"
      >
        <v-card>
          <v-form
            :disabled="form.processing"
            @submit.prevent="deleteUser"
          >
            <v-card-title>
              Delete Account
            </v-card-title>

            <v-card-text>
              <p class="mb-4">
                Are you sure you want to delete your account? Once your account is deleted, all of its resources and
                data will be permanently deleted. Please enter your password to confirm you would like to permanently
                delete your account.
              </p>

              <v-text-field
                ref="passwordInput"
                v-model="form.password"
                variant="outlined"
                type="password"
                label="Password"
                autocomplete="current-password"
                :error-messages="form.errors.password"
                :append-inner-icon="showP ? mdiEye : mdiEyeOff"
                hide-details="auto"
                @click:append-inner="showP = !showP"
              />
            </v-card-text>

            <v-card-actions>
              <v-btn
                :disabled="form.processing"
                @click="closeModal"
              >
                Cancel
              </v-btn>

              <v-btn
                :disabled="form.processing"
                type="submit"
                color="error"
              >
                Delete Account
              </v-btn>
            </v-card-actions>
          </v-form>
        </v-card>
      </v-dialog>
    </template>
  </ActionSection>
</template>
