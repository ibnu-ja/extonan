<template>
  <span>
    <span @click="startConfirmingPassword">
      <slot />
    </span>

    <v-dialog
      v-model="confirmingPassword"
      max-width="672"
    >
      <v-card>
        <v-card-title class="headline">
          {{ title }}
        </v-card-title>

        <v-card-text>
          {{ content }}

          <div class="mt-4">
            <v-text-field
              v-model="form.password"
              type="password"
              class="mt-1"
              label="Password"
              outlined
              :error-messages="form.error"
              @keyup.enter.native="confirmPassword"
            />
          </div>
        </v-card-text>

        <v-card-actions class="px-6 py-4">
          <v-spacer />
          <v-btn
            outlined
            @click.native="closeModal"
          >
            Nevermind
          </v-btn>

          <v-btn
            color="secondary"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click.native="confirmPassword"
          >
            {{ button }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </span>
</template>

<script>
/* eslint-disable vue/require-prop-types */
export default {
  props: {
    title: {
      default: 'Confirm Password'
    },
    content: {
      default: 'For your security, please confirm your password to continue.'
    },
    button: {
      default: 'Confirm'
    }
  },

  data () {
    return {
      confirmingPassword: false,
      form: {
        password: '',
        error: ''
      }
    }
  },

  methods: {
    startConfirmingPassword () {
      this.axios.get(this.route('password.confirmation')).then(response => {
        if (response.data.confirmed) {
          this.$emit('confirmed')
        } else {
          this.confirmingPassword = true

          setTimeout(() => this.$refs.password.focus(), 250)
        }
      })
    },

    confirmPassword () {
      this.form.processing = true

      this.axios
        .post(this.route('password.confirm'), {
          password: this.form.password
        })
        .then(() => {
          this.form.processing = false
          this.closeModal()
          this.$nextTick(() => this.$emit('confirmed'))
        })
        .catch(error => {
          this.form.processing = false
          this.form.error = error.response.data.errors.password[0]
          this.$refs.password.focus()
        })
    },

    closeModal () {
      this.confirmingPassword = false
      this.form.password = ''
      this.form.error = ''
    }
  }
}
</script>

<style scoped>
v-card-actions {
  background-color: #f3f4f6;
}
</style>
