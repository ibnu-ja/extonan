// Vuetify Documentation https://vuetifyjs.com

import Vue from 'vue'
import Vuetify, { VBtn } from 'vuetify/lib'
import id from 'vuetify/lib/locale/id'
import { Ripple } from 'vuetify/lib/directives'
// Vuetify's CSS styles
import 'vuetify/dist/vuetify.min.css'

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
        primary: '#FFA800',
        secondary: '#FF4343',
        accent: '#204165'
      },
      dark: {
        primary: '#50778D',
        secondary: '#0B1C3D',
        accent: '#204165'
      }
    },
    options: { customProperties: true }
  }
})

export default vuetify
