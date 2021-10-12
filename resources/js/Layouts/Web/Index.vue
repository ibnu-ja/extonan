<template>
  <v-app>
    <v-app-bar
      app
      elevate-on-scroll
    >
      <v-spacer />

      <!-- TODO logo -->
      <inertia-link href="/">
        <v-avatar
          color="grey darken-1"
          size="32"
        />
      </inertia-link>

      <v-text-field
        placeholder="Search ..."
        autocomplete="off"
        class="mx-2 mx-md-4"
        dense
        hide-details
        solo
        style="max-width: 400px;"
      >
        <template #prepend-inner>
          <v-icon class="ml-1 mr-2">
            mdi-magnify
          </v-icon>
          <!-- <v-icon
            :color="!isFocused ? 'grey' : undefined"
            class="ml-1 mr-2"
          >
            mdi-magnify
          </v-icon> -->
        </template>
      </v-text-field>

      <inertia-link
        v-for="test in 3"
        :key="test"
        as="v-btn"
        text
        class="d-none d-sm-flex mx-1 text-capitalize"
        href="/"
      >
        Album
      </inertia-link>

      <v-divider
        vertical
        inset
        class="mx-1"
      />

      <inertia-link
        v-if="$page.props.user"
        as="v-btn"
        text
        class="d-none d-sm-flex mx-1 text-capitalize"
        href="/dashboard"
      >
        Dashboard
      </inertia-link>

      <template v-else>
        <inertia-link
          v-if="!route().current('login') && !route().current('register')"
          :href="route('login')"
          as="v-btn"
          class="mx-1"
          text
          outlined
        >
          Login
        </inertia-link>
        <inertia-link
          v-if="!route().current('login') && !route().current('register')"
          :href="route('register')"
          as="v-btn"
          color="primary"
          class="mx-1"
        >
          Register
        </inertia-link>

        <div v-if="route().current('login') || route().current('register')">
          <transition
            name="slide-fade"
            mode="out-in"
          >
            <div
              v-if="route().current('login')"
              key="registerLink"
              class="mx-1"
            >
              Don't have an account?
              <inertia-link :href="route('register')">
                Sign up
              </inertia-link>
            </div>
            <div
              v-else
              key="loginLink"
              class="mx-1"
            >
              Already have an account?
              <inertia-link :href="route('login')">
                Sign in
              </inertia-link>
            </div>
          </transition>
        </div>
      </template>
      <home-settings />
      <v-spacer />
    </v-app-bar>

    <v-main>
      <v-container>
        <slot />
      </v-container>
    </v-main>

    <v-footer padless>
      <v-row
        justify="center"
        no-gutters
      >
        <v-col
          class="py-4 text-center"
          cols="12"
        >
          © AreWebs
          <!--2021 - -->
          {{ new Date().getFullYear() }}
        </v-col>
      </v-row>
    </v-footer>
  </v-app>
</template>

<script>
import { VBtn } from 'vuetify/lib'
import HomeSettings from '../Settings/Index.vue'

export default {
  name: 'WebLayout',
  components: {
    HomeSettings,
    // eslint-disable-next-line vue/no-unused-components
    VBtn
  }
}
</script>

<style>
/* Transitions */
.slide-fade-enter-active {
  transition: all 0.3s ease;
}
.slide-fade-leave-active {
  transition: all 0.8s cubic-bezier(1, 0.5, 0.8, 1);
}
.slide-fade-enter, .slide-fade-leave-to
    /* .slide-fade-leave-active below version 2.1.8 */ {
  transform: translateX(10px);
  opacity: 0;
}
</style>
