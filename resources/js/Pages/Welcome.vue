<template>
  <web-layout>
    <v-row>
      <v-col
        cols="12"
        md="9"
        order-md="2"
      >
        <v-card :tile="$vuetify.breakpoint.smAndDown">
          <v-card-title>
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
          </v-card-title>
          <v-container class="px-4 pb-4 pt-0">
            aaa
          </v-container>
          <v-card-actions><v-btn>a</v-btn></v-card-actions>
        </v-card>
      </v-col>
      <v-col
        cols="12"
        md="3"
        order-md="1"
      >
        <v-card :tile="$vuetify.breakpoint.smAndDown">
          <v-card-title>
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
          </v-card-title>
          <v-container class="px-4 pb-4 pt-0">
            aaa
          </v-container>
          <v-card-actions><v-btn>a</v-btn></v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </web-layout>
</template>

<script>
import WebLayout from '@/Layouts/Web/Index.vue'
export default {
  components: { WebLayout },
  data () {
    return {
      signUpEmail: false,
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

  created () {
    console.log(this.$vuetify.breakpoint.smAndDown)
  },

  methods: {
    submit () {
      this.form.password_confirmation = this.form.password
      this.form.post(this.route('register'), {
        onFinish: () => this.form.reset('password', 'password_confirmation')
      })
    }
  }
}
</script>
