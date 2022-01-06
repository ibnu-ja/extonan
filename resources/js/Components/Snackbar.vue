<template>
  <!-- TODO fix timeout -->
  <v-snackbar
    v-model="val"
    top
    :color="snackbar.color"
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
      val: false
    }
  },
  computed: {
    snackbar () {
      return this.$page.props.snackbar
    }
  },
  watch: {
    snackbar () {
      this.val = true
    }
  }
}
</script>
