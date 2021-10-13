<template>
  <v-list-item
    :href="item.href"
    :rel="item.href ? 'nofollow' : undefined"
    :target="item.href ? '_blank' : undefined"
    :to="item.to"
    v-bind="$attrs"
    :class="{
      'primary white--text v-item--active v-list-item--active': active(
        item.exact,
        item.to
      )
    }"
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
      console.log(this.item)
      const matches = this.item.title.match(/\b(\w)/g)
      return matches.join('')
    }
  },

  methods: {
    active (exact, link) {
      if (exact) return link === this.$page.url
      else return this.$page.url.startsWith(link)
    }
  }
}
</script>
