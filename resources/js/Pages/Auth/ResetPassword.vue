<template>
  <web-layout>
    <v-row justify="center">
      <v-col
        cols="10"
        sm="8"
        md="6"
      >
        <v-card class="p-2">
          <v-card-title>Reset Password</v-card-title>
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
                v-model="form.email"
                dense
                outlined
                label="Email Address"
                required
                autofocus
              />
              <v-text-field
                v-model="form.password"
                dense
                outlined
                label="New Password"
                required
                autocomplete="current-password"
                :append-icon="show_pass ? 'mdi-eye' : 'mdi-eye-off'"
                :type="show_pass ? 'text' : 'password'"
                hint="At least 8 characters"
                @click:append="show_pass = !show_pass"
              />
              <v-text-field
                v-model="form.password_confirmation"
                dense
                outlined
                label="Comfirn New Password"
                required
                autocomplete="current-password"
                :append-icon="
                  show_pass_confirmation ? 'mdi-eye' : 'mdi-eye-off'
                "
                :type="show_pass_confirmation ? 'text' : 'password'"
                hint="At least 8 characters"
                @click:append="show_pass_confirmation = !show_pass_confirmation"
              />
              <v-btn
                block
                color="primary"
                type="submit"
                :disabled="form.processing"
              >
                Reset Password
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
    <v-sheet
      class=""
      color="gray pulse"
      width="200px"
    />
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

  props: {
    // eslint-disable-next-line vue/require-default-prop
    email: String,
    // eslint-disable-next-line vue/require-default-prop
    token: String
  },

  data () {
    return {
      show_pass_confirmation: false,
      show_pass: false,
      form: this.$inertia.form({
        token: this.token,
        email: this.email,
        password: '',
        password_confirmation: ''
      })
    }
  },

  methods: {
    submit () {
      this.form.post(this.route('password.update'), {
        onFinish: () => this.form.reset('password', 'password_confirmation')
      })
    }
  }
}
</script>
