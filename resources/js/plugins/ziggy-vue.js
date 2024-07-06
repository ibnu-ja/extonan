import { route } from 'ziggy-js'
import { ziggySymbol } from '@/symbols/ziggySymbol'

export const ZiggyVue = {
  install(app, options) {
    const r = (name, params, absolute, config = options) =>
      route(name, params, absolute, config)

    app.provide(ziggySymbol, r)
    app.config.globalProperties.route = r
  },
}
