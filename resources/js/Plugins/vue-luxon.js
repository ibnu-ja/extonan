import Vue from 'vue'
import VueLuxon from 'vue-luxon'

export default Vue.use(VueLuxon, {
  input: {
    zone: 'utc',
    format: 'iso'
  },
  output: 'short'
})
