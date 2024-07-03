// Styles
import '../../css/app.scss'

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
// import { md3 } from 'vuetify/blueprints'
import { aliases, mdi } from 'vuetify/iconsets/mdi-svg'

const vuetify = createVuetify({
  // blueprint: md3,
  icons: {
    defaultSet: 'mdi',
    aliases,
    sets: {
      mdi,
    },
  },
})

export default vuetify
