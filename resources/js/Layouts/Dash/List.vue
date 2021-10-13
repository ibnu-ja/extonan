<template>
  <v-list
    expand
    nav
    dense
    v-bind="$attrs"
    v-on="$listeners"
  >
    <slot name="user" />
    <template v-for="(item, i) in items">
      <v-subheader
        v-if="item.header"
        :key="`subhead-${i}`"
        :inset="!mini"
        v-text="mini ? item.mini : item.header"
      />
      <v-divider
        v-else-if="item.divider"
        :key="`divider-${i}`"
        :inset="item.inset"
      />
      <list-group
        v-else-if="item.items"
        :key="`group-${i}`"
        :item="item"
      />

      <list-item
        v-else
        :key="`item-${i}`"
        :item="item"
      />
    </template>
    <slot name="extra" />
  </v-list>
</template>

<script>
import { get } from 'vuex-pathify'
export default {
  name: 'DefaultList',

  components: {
    ListGroup: () => import('./ListGroup'),
    ListItem: () => import('./ListItem')
  },

  props: {
    items: {
      type: Array,
      default: () => []
    }
  },
  computed: {
    ...get('dashboard', ['mini'])
  }
}
</script>
