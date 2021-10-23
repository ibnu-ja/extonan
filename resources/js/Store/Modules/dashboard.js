// Utilities
import { make } from 'vuex-pathify'

const state = {
  mini: false,
  drawer: null,
  loading: false
}
// aaa

const mutations = make.mutations(state)

const actions = {
  ...make.actions(state)
}

const getters = {}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
