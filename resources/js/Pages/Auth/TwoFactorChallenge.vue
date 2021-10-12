<template>
  <web-layout>
    <v-row justify="center">
      <v-col
        cols="10"
        sm="8"
        md="6"
      >
        <v-card class="p-2">
          <v-card-title>Two Factor authentication</v-card-title>
          <v-card-text>
            <validation-errors class="mb-4" />

            <div
              v-if="status"
              class="mb-4 font-medium text-sm text-green-600"
            >
              {{ status }}
            </div>

            <form @submit.prevent="submit">
              <v-text-field
                v-if="!recovery"
                id="code"
                ref="code"
                v-model="form.code"
                type="text"
                inputmode="numeric"
                class="mt-1 block w-full"
                autofocus
                autocomplete="one-time-code"
                dense
                outlined
                label="Code"
                required
              />
              <v-text-field
                v-else
                id="recovery_code"
                ref="recovery_code"
                v-model="form.recovery_code"
                type="text"
                inputmode="numeric"
                class="mt-1 block w-full"
                autofocus
                autocomplete="one-time-code"
                dense
                outlined
                label="Recovery Code"
                required
              />
              <v-btn
                block
                color="primary"
                type="submit"
                :disabled="form.processing"
              >
                Sign In
              </v-btn>
            </form>
            <div class="my-3 d-flex justify-space-between text-body-1">
              <a
                href="#"
                @click.prevent="toggleRecovery"
                v-text="
                  !recovery ? 'Use recovery code' : 'Use authenticator code'
                "
              />

              <div>
                Don't have an account?
                <inertia-link :href="route('register')">
                  Sign up
                </inertia-link>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </web-layout>
</template>

<script>
import ValidationErrors from '@/Components/ValidationErrors'
import WebLayout from '@/Layouts/Web/Index.vue'

export default {
  components: {
    ValidationErrors,
    WebLayout
  },

  data () {
    return {
      recovery: false,
      form: this.$inertia.form({
        code: '',
        recovery_code: ''
      })
    }
  },

  methods: {
    toggleRecovery () {
      this.recovery ^= true

      this.$nextTick(() => {
        if (this.recovery) {
          this.$refs.recovery_code.focus()
          this.form.code = ''
        } else {
          this.$refs.code.focus()
          this.form.recovery_code = ''
        }
      })
    },

    submit () {
      this.form.post(this.route('two-factor.login'))
    }
  }
}
</script>
