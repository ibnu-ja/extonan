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
        @click="hasMessage = false"
      >
        Close
      </v-btn>
    </template>
  </v-snackbar>
</template>

<script>
export default {
  name: 'BaseSnackbar',
  data () {
    return {
      hasMessage: !!this.$page.props.snackbar.message
    }
  },
  computed: {
    snackbar () {
      return this.$page.props.snackbar
    }
  }
}
</script>
