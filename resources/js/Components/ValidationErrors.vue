<template>
  <v-alert
    v-if="hasErrors"
    dense
    text
    border="left"
    type="error"
    class="mb-0"
  >
    Whoops! Something went wrong.

    <ul class="">
      <li
        v-for="(error, key) in errors"
        :key="key"
      >
        {{ error }}
      </li>
    </ul>
  </v-alert>
</template>

<script>
export default {
  computed: {
    errors () {
      return this.$page.props.errors
    },

    hasErrors () {
      return Object.keys(this.errors).length > 0
    }
  }
}
</script>
