<template>
  <web-layout>
    <v-row justify="center">
      <v-col
        cols="10"
        sm="8"
        md="6"
        align-self="center"
      >
        <v-card class="p-2">
          <v-card-title>Confirm Password</v-card-title>
          <v-card-text>
            <div class="mb-4">
              This is a secure area of the application. Please confirm your
              password before continuing.
            </div>

            <validation-errors class="mb-4" />

            <form @submit.prevent="submit">
              <v-text-field
                v-model="form.password"
                dense
                outlined
                label="Password"
                required
                autocomplete="current-password"
                :append-icon="showP ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showP ? 'text' : 'password'"
                hint="At least 8 characters"
                @click:append="showP = !showP"
              />
              <v-btn
                block
                color="primary"
                type="submit"
                :disabled="form.processing"
              >
                Confirm
              </v-btn>
            </form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </web-layout>
</template>

<script>
import WebLayout from '@/Layouts/Web/Index.vue'
export default {
  components: {
    WebLayout
  },

  data () {
    return {
      form: this.$inertia.form({
        password: ''
      }),
      showP: false
    }
  },

  methods: {
    submit () {
      this.form.post(this.route('password.confirm'), {
        onFinish: () => this.form.reset()
      })
    }
  }
}
</script>
