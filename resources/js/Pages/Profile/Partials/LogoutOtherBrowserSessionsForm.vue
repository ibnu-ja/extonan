<script setup lang="ts">
import { inject, ref, VNodeRef } from 'vue'
import { useForm } from '@inertiajs/vue3'
import ActionSection from '@/Components/ActionSection.vue'
import { UserSession } from '@/types'
import { mdiCellphone, mdiDesktopTowerMonitor, mdiEye, mdiEyeOff } from '@mdi/js'
import { route as ziggyRoute } from 'ziggy-js'

defineProps<{
  sessions: UserSession[]
}>()

const route = inject('route') as typeof ziggyRoute

const confirmingLogout = ref(false)
const passwordInput = ref<VNodeRef | null>(null)
const showP = ref(false)

const form = useForm({
  password: '',
})

const confirmLogout = () => {
  confirmingLogout.value = true

  setTimeout(() => passwordInput.value.focus(), 250)
}

const logoutOtherBrowserSessions = () => {
  form.delete(route('other-browser-sessions.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value.focus(),
    onFinish: () => form.reset(),
  })
}

const closeModal = () => {
  confirmingLogout.value = false

  form.reset()
}
</script>

<template>
  <ActionSection>
    <template #title>
      Browser Sessions
    </template>

    <template #description>
      Manage and log out your active sessions on other browsers and devices.
    </template>

    <template #content>
      <v-card-text>
        <div class="mb-4">
          If necessary, you may log out of all of your other browser sessions across all of your devices.
          Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your
          account has been compromised, you should also update your password.
        </div>
      </v-card-text>
      <!-- Other Browser Sessions -->
      <v-list
        v-if="sessions.length > 0"
      >
        <v-list-item
          v-for="(session, i) in sessions"
          :key="i"
          :active="session.is_current_device"
          color="primary"
        >
          <template #prepend>
            <v-icon
              start
              :icon="session.agent?.is_desktop ? mdiDesktopTowerMonitor : mdiCellphone"
              size="40"
            />
          </template>
          <v-list-item-title>
            {{ session.agent?.platform }} -
            {{ session.agent?.browser }}
          </v-list-item-title>
          <v-list-item-subtitle>
            {{ session.ip_address }},
            <span
              v-if="session.is_current_device"
              class="text-success"
            >
              This device
            </span>
            <span v-else>Last active {{ session.last_active }}</span>
          </v-list-item-subtitle>
        </v-list-item>
      </v-list>
      <v-card-actions>
        <v-btn
          @click="confirmLogout"
        >
          Log Out Other Browser Sessions
        </v-btn>

        <v-scroll-x-transition>
          <div v-show="form.recentlySuccessful">
            Done.
          </div>
        </v-scroll-x-transition>
      </v-card-actions>

      <!-- Log Out Other Devices Confirmation Modal -->
      <v-dialog
        v-model="confirmingLogout"
        max-width="600"
      >
        <v-card>
          <v-form
            :disabled="form.processing"
            @submit.prevent="logoutOtherBrowserSessions"
          >
            <v-card-title>
              Log Out Other Browser Sessions
            </v-card-title>
            <v-card-text>
              <p class="mb-4">
                Please enter your password to confirm you would like to log out of your other browser sessions across
                all of
                your devices.
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
              <v-spacer />
              <v-btn
                :disabled="form.processing"
                @click="closeModal"
              >
                Cancel
              </v-btn>

              <v-btn
                type="submit"
                color="primary"
                :disabled="form.processing"
              >
                Log Out Other Browser Sessions
              </v-btn>
            </v-card-actions>
          </v-form>
        </v-card>
      </v-dialog>
    </template>
  </ActionSection>
</template>
