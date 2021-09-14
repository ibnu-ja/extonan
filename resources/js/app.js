// Import modules...
import Vue from 'vue'
import { ZiggyVue } from 'ziggy'
import { Ziggy } from './ziggy'
import axios from 'axios'
import VueAxios from 'vue-axios'
import {
  App as InertiaApp,
  plugin as InertiaPlugin
} from '@inertiajs/inertia-vue'
// Vue.use(PortalVue);

// Plugins
import vuetify from '@/Plugins/vuetify'

require('./bootstrap')

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

Vue.use(VueAxios, axios)

Vue.use(ZiggyVue, Ziggy)
// Vue.mixin({ methods: { route } })
Vue.use(InertiaPlugin)

const app = document.getElementById('app')

new Vue({
  vuetify,
  render: h =>
    h(InertiaApp, {
      props: {
        initialPage: JSON.parse(app.dataset.page),
        resolveComponent: name => require(`./Pages/${name}`).default
      }
    })
}).$mount(app)
