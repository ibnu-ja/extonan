<template>
  <web-layout>
    <v-row justify="center">
      <v-col
        cols="10"
        sm="8"
        md="7"
      >
        <v-card class="py-4 px-4 mt-10">
          <v-card-title>Forgot password?</v-card-title>
          <v-card-subtitle>No problem.</v-card-subtitle>
          <v-card-text>
            <div class="mb-6">
              Just let us know your email address and we will email you a
              password reset link that will allow you to choose a new one.
            </div>

            <div
              v-if="status"
              class="mb-4 success--text"
            >
              {{ status }}
            </div>

            <validation-errors class="mb-4" />

            <form @submit.prevent="submit">
              <v-text-field
                v-model="form.email"
                dense
                outlined
                label="Email Address"
                required
                autofocus
                type="email"
              />

              <v-btn
                block
                color="primary"
                type="submit"
                :disabled="form.processing"
              >
                Request Password Reset
              </v-btn>
            </form>
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
    status: String
  },

  data () {
    return {
      form: this.$inertia.form({
        email: ''
      })
    }
  },

  methods: {
    submit () {
      this.form.post(this.route('password.email'))
    }
  }
}
</script>
