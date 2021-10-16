<template>
  <app-user-section @submitted="setPassword">
    <template #title>
      Update Password
    </template>

    <template #description>
      Ensure your account is using a long, random password to stay secure.
    </template>

    <template #content>
      <v-text-field
        ref="password"
        v-model="form.password"
        outlined
        label="Password"
        required
        autocomplete="new-password"
        :error-messages="form.errors.password"
        :append-icon="showP ? 'mdi-eye' : 'mdi-eye-off'"
        :type="showP ? 'text' : 'password'"
        hint="At least 8 characters"
        @click:append="showP = !showP"
      />

      <v-text-field
        v-model="form.password_confirmation"
        outlined
        label="Confirm Password"
        required
        autocomplete="new-password"
        :error-messages="form.errors.password_confirmation"
        :append-icon="showNP ? 'mdi-eye' : 'mdi-eye-off'"
        :type="showNP ? 'text' : 'password'"
        @click:append="showNP = !showNP"
      />
    </template>

    <template #actions>
      <transition
        leave-active-class="transition ease-in duration-1000"
        leave-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-show="form.recentlySuccessful"
          class="text-sm text-gray-600"
        >
          Saved.
        </div>
      </transition>

      <v-spacer />

      <v-btn
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        type="submit"
        color="secondary"
      >
        Save
      </v-btn>
    </template>
  </app-user-section>
</template>

<script>
import AppUserSection from '@/Components/UserSection'

export default {
  components: {
    AppUserSection
  },

  data () {
    return {
      showP: false,
      showNP: false,
      form: this.$inertia.form({
        password: '',
        password_confirmation: ''
      })
    }
  },

  methods: {
    setPassword () {
      this.form.put(this.route('user-password.set'), {
        errorBag: 'setPassword',
        preserveScroll: true,
        onSuccess: () => this.form.reset(),
        onError: () => {
          if (this.form.errors.password) {
            this.form.reset('password', 'password_confirmation')
            this.$nextTick(() => {
              setTimeout(() => {
                this.$refs.password.focus()
              })
            })
          }
        }
      })
    }
  }
}
</script>
