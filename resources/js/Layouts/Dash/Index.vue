<template>
  <v-app>
    <v-app-bar
      id="app-nav"
      app
      fixed
      clipped-left
      dense
    >
      <snackbar />
      <v-app-bar-nav-icon
        class="d-lg-none"
        @click.stop="drawer = !drawer"
      />

      <!-- TODO Logo -->

      <!-- Logo -->
      <inertia-link
        href="/"
        as="v-btn"
        icon
      >
        <v-icon>mdi-home</v-icon>
      </inertia-link>
      <tambah-dropdown />
      <v-divider
        vertical
        inset
        class="
        mx-2"
      />

      <v-toolbar-title>
        <header
          v-if="$slots.header"
          id="pageheader"
        >
          <slot name="header" />
        </header>
      </v-toolbar-title>

      <v-spacer />

      <home-settings />

      <!-- Teams Dropdown -->
      <teams-dropdown />

      <!-- Settings Dropdown -->
      <v-menu
        offset-y
        transition="slide-y-transition"
        min-width="200"
      >
        <template #activator="{ on, attrs }">
          <v-avatar
            v-if="$page.props.jetstream.managesProfilePhotos"
            v-bind="attrs"
            v-on="on"
          >
            <img
              :src="$page.props.user.profile_photo_url"
              :alt="$page.props.user.name"
            >
          </v-avatar>
          <v-btn
            v-else
            text
            v-bind="attrs"
            class="text-capitalize d-none d-sm-flex"
            v-on="on"
          >
            {{ $page.props.user.name }}

            <svg
              class=" -mr-0.5 "
              style="width: 1.rem; height: 1rem; "
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </v-btn>
        </template>
        <v-list dense>
          <inertia-link
            as="v-list-item"
            href="/"
          >
            Home
          </inertia-link>
          <v-subheader>Manage Account</v-subheader>
          <inertia-link
            class="v-list-item"
            as="v-list-item"
            :href="route('profile.show')"
          >
            Profile
          </inertia-link>
          <inertia-link
            v-if="$page.props.jetstream.hasApiFeatures"
            as="v-list-item"
            :href="route('api-tokens.index')"
          >
            API Tokens
          </inertia-link>
          <v-divider />
          <!-- Authentication -->
          <v-list-item @click.prevent="logout">
            Logout
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>

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
import HomeSettings from '../Settings/Index.vue'
import TambahDropdown from './TambahDropdown.vue'
import TeamsDropdown from './TeamsDropdown.vue'
export default {
  name: 'DashLayout',
  components: {
    HomeSettings,
    TambahDropdown,
    TeamsDropdown,
    Drawer: () => import('./Drawer.vue'),
    Snackbar
  },

  data () {
    return {
      drawer: true,
      mini: false,
      showingNavigationDropdown: false
    }
  },

  methods: {
    logout () {
      this.$inertia.post(this.route('logout'))
    }
  }
}
</script>
