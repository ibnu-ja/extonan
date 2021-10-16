<template>
  <web-layout>
    <v-row justify="center">
      <v-col
        cols="10"
        sm="8"
        md="6"
      >
        <v-card>
          <v-card-title>Sign Up with Email</v-card-title>

          <v-card-text>
            <validation-errors class="mb-4" />

            <p>Please fill registration form below.</p>

            <form @submit.prevent="submit">
              <v-text-field
                v-model="form.name"
                required
                autofocus
                dense
                outlined
                label="Full Name"
              />
              <v-text-field
                v-model="form.email"
                required
                dense
                outlined
                label="Email address"
              />
              <v-text-field
                v-model="form.password"
                dense
                outlined
                label="Password"
                required
                autocomplete="new-password"
                :append-icon="showP ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showP ? 'text' : 'password'"
                hint="At least 8 characters"
                @click:append="showP = !showP"
              />
              <v-text-field
                v-model="form.password_confirmation"
                dense
                outlined
                label="Confirm Password"
                required
                autocomplete="new-password"
                :append-icon="showCP ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showCP ? 'text' : 'password'"
                hint="At least 8 characters"
                @click:append="showCP = !showCP"
              />
              <v-btn
                color="success"
                block
                type="submit"
                :disabled="form.processing"
              >
                Register
              </v-btn>
            </form>

            <socialstream-providers
              v-if="$page.props.socialstream.show"
              action="Sign Up"
            />
          </v-card-text>

          <!-- TODO tos & privacy policy dialog -->
          <v-card-text
            class="mb-3"
            style="font-size: 14px;"
          >
            By signing up you agree to the
            <inertia-link href="register">
              Terms of Service
            </inertia-link>
            and
            <inertia-link href="register">
              Privacy Policy
            </inertia-link>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </web-layout>
</template>

<script>
import ValidationErrors from '@/Components/ValidationErrors'
import WebLayout from '@/Layouts/Web/Index.vue'

import SocialstreamProviders from '@/Socialstream/Providers.vue'

export default {
  components: {
    ValidationErrors,
    SocialstreamProviders,
    WebLayout
  },

  data () {
    return {
      showP: false,
      showCP: false,
      form: this.$inertia.form({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        terms: true
      })
    }
  },

  methods: {
    submit () {
      this.form.post(this.route('register'), {
        onFinish: () => this.form.reset('password', 'password_confirmation')
      })
    }
  }
}
</script>

<style></style>
