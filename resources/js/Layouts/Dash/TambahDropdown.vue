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
        :key="item.to"
        as="v-list-item"
        :href="item.to"
        :aria-selected="$page.url === item.to"
        :class="{
          'primary white--text v-list-item--active': $page.url === item.to
        }"
      >
        <v-list-item-icon> <v-icon v-text="item.icon" /> </v-list-item-icon>
        <v-list-item-content v-text="item.text" />
      </inertia-link>
    </v-list>
  </v-menu>
</template>

<script>
import { VListItem } from 'vuetify/lib'
export default {
  // eslint-disable-next-line vue/no-unused-components
  components: { VListItem },
  data: () => ({
    selectedItem: 1,
    items: [
      { text: 'Album', icon: 'mdi-clock', to: '/dashboard/album/create' },
      { text: 'Artist', icon: 'mdi-account', to: '/dashboard/artist/create' },
      {
        text: 'Product',
        icon: 'mdi-package-variant',
        to: '/dashboard/product/new'
      },
      {
        text: 'Organizations',
        icon: 'mdi-account-group',
        to: '/dashboard/org/new'
      },
      { text: 'Event', icon: 'mdi-calendar', to: '/dashboard/event/new' }
    ]
  })
}
</script>
