<template>
  <v-menu
    open-on-hover
    bottom
    offset-y
  >
    <template #activator="{ on, attrs }">
      <v-btn
        v-bind="attrs"
        text
        class="d-none d-sm-flex text-capitalize"
        v-on="on"
      >
        <v-icon left>
          mdi-plus
        </v-icon>
        Tambah
      </v-btn>
    </template>

    <v-list dense>
      <inertia-link
        v-for="item in items"
        :key="getDirectLink(item.to)"
        as="v-list-item"
        :to="getDirectLink(item.to)"
        :class="{
          'primary white--text v-list-item--active': checkIsCurrentRoute(
            item.exact,
            item.to
          )
        }"
      >
        <v-list-item-icon> <v-icon v-text="item.icon" /> </v-list-item-icon>
        <v-list-item-content v-text="item.text" />
      </inertia-link>
    </v-list>
  </v-menu>
</template>

<script>
import { has } from 'lodash'
// WKWK
// note to self: read this https://github.com/tighten/ziggy#the-router-class
import { VListItem } from 'vuetify/lib'
export default {
  // eslint-disable-next-line vue/no-unused-components
  components: { VListItem },
  data () {
    return {
      selectedItem: 1,
      items: [
        {
          text: 'Album',
          icon: 'mdi-album',
          to: {
            name: 'album.create'
          }
        },
        {
          text: 'Artist',
          icon: 'mdi-account',
          exact: true,
          to: {
            name: 'artist.create'
          }
        },
        {
          text: 'Product',
          icon: 'mdi-package-variant',
          exact: true,
          to: {
            name: 'product.create'
          }
        },
        {
          text: 'Organizations',
          icon: 'mdi-account-group',
          exact: true,
          to: {
            name: 'organization.create'
          }
        },
        {
          text: 'Event',
          icon: 'mdi-calendar',
          exact: true,
          to: { name: 'event.create' }
        }
      ]
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
        // if it needs route() instance
        if (exact) {
          return this.route().current(route.name)
        } else {
          return this.route().current(route.name.replace(/\.([^.]*)$/, '*'))
        }
      } else {
        // it's plain url
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
