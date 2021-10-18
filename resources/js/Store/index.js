import Vue from 'vue'
import Vuex from 'vuex'

import pathify from 'vuex-pathify'
import VuexPersistence from 'vuex-persist'

import * as modules from './Modules'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

const vuexLocal = new VuexPersistence({
  storage: window.localStorage
})

export default new Vuex.Store({
  plugins: [pathify.plugin, vuexLocal.plugin],
  modules,

  strict: debug
})
