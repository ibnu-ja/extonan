// Vuetify Documentation https://vuetifyjs.com

import Vue from 'vue'
import Vuetify, { VBtn } from 'vuetify/lib'
import id from 'vuetify/lib/locale/id'
import { Ripple } from 'vuetify/lib/directives'
import '@mdi/font/css/materialdesignicons.css'

Vue.use(Vuetify, {
  directives: {
    Ripple
  },
  components: {
    VBtn
  }
})

const vuetify = new Vuetify({
  lang: {
    locales: { id },
    current: 'id'
  },
  theme: {
    dark: false,
    themes: {
      light: {
        primary: '#7e2c59',
        secondary: '#314094',
        accent: '#2c7e43'
      },
      dark: {
        primary: '#df76b0',
        secondary: '#2e4e8d',
        accent: '#59bb75'
      }
    },
    options: { customProperties: true }
  }
})

export default vuetify
