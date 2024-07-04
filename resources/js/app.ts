import './bootstrap'
import '../css/app.scss'

import { createApp, h, DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from 'ziggy-js'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'
import { Config } from 'ziggy-js'

import * as Routes from './routes.json'
import Vuetify from '@/plugins/vuetify'
import pinia from '@/plugins/pinia'
const config = Routes as Config

createInertiaApp({
  title: title => `${title} - ${appName}`,
  resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue, config)
      .use(Vuetify)
      .use(pinia)
      .mount(el)
  },
  progress: {
    color: '#4B5563',
  },

}).then()
