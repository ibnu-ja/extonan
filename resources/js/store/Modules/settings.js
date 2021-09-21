// Utilities
import { make } from 'vuex-pathify'
import merge from 'lodash/merge'

const state = () => {
  // eslint-disable-next-line no-var
  var data = JSON.parse(localStorage.getItem('shinrai@settings')) || {}

  return merge(
    {
      theme: {
        dark: false,
        system: false
      }
    },
    data
  )
}

const mutations = make.mutations(state)

const actions = {
  ...make.actions(state)
  // init: async ({ dispatch }) => {
  //   //
  // }
}

// const actions = {
//   fetch ({ commit }) {
//     // if (!IN_BROWSER) return

//     console.log('fetch')

//     const data = state()

//     for (const key in data) {
//       commit(key, data[key])
//     }
//   },
//   update ({ state }) {
//     // if (!IN_BROWSER) return

//     console.log('update')

//     localStorage.setItem('shinrai@settings', JSON.stringify(state))
//   }
// }

const getters = {}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
