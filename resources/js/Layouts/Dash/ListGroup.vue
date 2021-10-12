<template>
  <v-list
    expand
    nav
    dense
    v-bind="$attrs"
    v-on="$listeners"
  >
    <template v-for="(item, i) in items">
      <v-subheader
        v-if="item.header"
        :key="`subhead-${i}`"
        inset
        v-text="item.header"
      />
      <v-divider
        v-else-if="item.divider"
        :key="`subhead-${i}`"
        inset
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
  </v-list>
</template>

<script>
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
  }
}
</script>
