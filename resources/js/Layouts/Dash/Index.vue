<template>
  <v-app>
    <app-bar>
      <template #header>
        <slot name="header" />
      </template>
    </app-bar>
    <snackbar />

    <drawer />

    <v-main>
      <v-container class="pt-7">
        <slot />
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import Snackbar from '../../Components/Snackbar.vue'
export default {
  name: 'DashLayout',
  components: {
    Drawer: () => import('./Drawer.vue'),
    AppBar: () => import('./AppBar.vue'),
    Snackbar
  }
}
</script>
