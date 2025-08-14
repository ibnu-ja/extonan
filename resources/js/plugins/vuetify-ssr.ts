// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
// import { md3 } from 'vuetify/blueprints'
import { aliases, mdi } from 'vuetify/iconsets/mdi-svg'

const vuetify = createVuetify({
  defaults: {
    VBtn: {
      class: 'text-none',
    },
  },
  icons: {
    defaultSet: 'mdi',
    aliases,
    sets: {
      mdi,
    },
  },
  ssr: true,
  theme: {
    defaultTheme: 'system',
  },
})

export default vuetify
