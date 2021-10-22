// Import modules...
import Vue from 'vue'
import { ZiggyVue } from 'ziggy'
import { Ziggy } from './ziggy'
import axios from 'axios'
import store from './Store'
import VueAxios from 'vue-axios'
import { createInertiaApp, Link } from '@inertiajs/inertia-vue'

// Plugins
import vuetify from '@/Plugins/vuetify'

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

Vue.use(VueAxios, axios)

Vue.use(ZiggyVue, Ziggy)
Vue.component('InertiaLink', Link)

createInertiaApp({
  resolve: name => require(`./Pages/${name}`),
  setup ({ el, App, props }) {
    new Vue({
      store,
      vuetify,
      render: h => h(App, props)
    }).$mount(el)
  }
})
