// Utilities
import { make } from 'vuex-pathify'

const state = {
  theme: {
    dark: null,
    system: true
  },
  color: {
    dark: {
      primary: '#df76b0'
    },
    light: {
      primary: '#df76b0'
    }
  }
}

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
