import './bootstrap'
// import '../css/app.scss'
import { createApp, DefineComponent, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import Vuetify from '@/plugins/vuetify'
import pinia from '@/plugins/pinia'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

router.on('finish', () => {
  // for vueuse useBrowserLocation
  // https://discord.com/channels/592327939920494592/915652236376436826/917067568303443980
  window.dispatchEvent(new PopStateEvent('popstate'))
})

createInertiaApp({
  title: title => `${title} - ${appName}`,
  resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(Vuetify)
      .use(ZiggyVue)
      .use(pinia)
      .mount(el)
  },
  progress: {
    color: '#4B5563',
  },

}).then()
