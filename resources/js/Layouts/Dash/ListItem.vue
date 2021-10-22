<template>
  <!-- TODO list condition -->
  <v-list-item
    :href="item.href"
    :rel="item.href ? 'nofollow' : undefined"
    :target="item.href ? '_blank' : undefined"
    :to="getDirectLink(item.to)"
    :class="{
      'primary white--text v-list-item--active': checkIsCurrentRoute(
        item.exact,
        item.to
      )
    }"
    :aria-selected="checkIsCurrentRoute(item.exact, item.to)"
    v-bind="$attrs"
    v-on="$listeners"
  >
    <v-list-item-icon v-if="!item.icon">
      {{ title }}
    </v-list-item-icon>

    <v-list-item-avatar v-if="item.avatar">
      <v-img :src="item.avatar" />
    </v-list-item-avatar>

    <v-list-item-icon v-if="item.icon">
      <v-icon v-text="item.icon" />
    </v-list-item-icon>

    <v-list-item-content v-if="item.title">
      <v-list-item-title v-text="item.title" />
    </v-list-item-content>
  </v-list-item>
</template>

<script>
import { has } from 'lodash'
export default {
  name: 'DefaultListItem',

  props: {
    item: {
      type: Object,
      default: () => ({})
    }
  },

  computed: {
    title () {
      const matches = this.item.title.match(/\b(\w)/g)
      return matches.join('')
    }
  },

  methods: {
    // TODO: remove repetitive use of code (route checking)
    checkIsRouteOrLink (route) {
      if (has(route, 'name')) {
        return true
      } else return false
    },
    getDirectLink (route) {
      if (has(route, 'name')) {
        return this.route(route.name, route.params)
      } else {
        return route
      }
    },
    checkIsCurrentRoute (exact, route) {
      if (this.checkIsRouteOrLink(route)) {
        if (exact) this.route().current(route.name)
        else {
          return this.route().current(route.name.replace(/\.([^.]*)$/, '*'))
        }
      } else {
        if (exact) {
          return route === this.$page.url
        } else {
          return this.$page.url.startsWith(route)
        }
      }
    }
  }
}
</script>
