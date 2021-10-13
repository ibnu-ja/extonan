<template>
  <v-snackbar
    v-model="hasMessage"
    top
    :color="snackbar.color"
    :timeout="snackbar.timeout"
    dismissible
  >
    {{ snackbar.message }}

    <template #action="{ attrs }">
      <v-btn
        text
        v-bind="attrs"
        @click="active = false"
      >
        Close
      </v-btn>
    </template>
  </v-snackbar>
</template>

<script>
export default {
  name: 'BaseSnackbar',
  computed: {
    snackbar () {
      return this.$page.props.snackbar
    },

    hasMessage () {
      return !!this.snackbar.message
    }
  }
}
</script>
