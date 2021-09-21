<template>
  <v-app>
    <v-app-bar
      id="app-nav"
      app
      fixed
      clipped-left
    >
      <v-app-bar-nav-icon
        class="d-lg-none"
        @click.stop="drawer = !drawer"
      />

      <!-- Logo -->

      <v-spacer />
      <!-- Navigation Links -->
      <inertia-link
        :href="route('dashboard')"
        :active="route().current('dashboard')"
        as="v-btn"
        text
        class="d-none d-sm-flex text-capitalize"
      >
        Dashboard
      </inertia-link>

      <home-settings />

      <!-- Teams Dropdown -->
      <v-menu
        v-if="$page.props.jetstream.hasTeamFeatures"
        offset-y
        transition="slide-y-transition"
        min-width="240"
      >
        <template #activator="{ on, attrs }">
          <v-btn
            text
            v-bind="attrs"
            class="d-none d-sm-flex"
            v-on="on"
          >
            {{ $page.props.user.current_team.name }}
            <svg
              style="width: 1rem; height: 1rem;"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </v-btn>
        </template>
        <v-list dense>
          <v-subheader>Manage Team</v-subheader>
          <inertia-link
            :href="route('teams.show', $page.props.user.current_team)"
            as="v-list-item"
          >
            Team Settings
          </inertia-link>
          <inertia-link
            v-if="$page.props.jetstream.canCreateTeams"
            :href="route('teams.create')"
            as="v-list-item"
          >
            Create New Team
          </inertia-link>

          <v-subheader>Switch Teams</v-subheader>
          <template v-for="team in $page.props.user.all_teams">
            <v-list-item
              :key="team.id"
              @click.prevent="switchToTeam(team)"
            >
              <v-list-item-icon
                v-if="team.id == $page.props.user.current_team_id"
              >
                <v-icon style="color: rgba(52, 211, 153, 1); ">
                  mdi-check-circle-outline
                </v-icon>
              </v-list-item-icon>
              <!-- <svg v-if="team.id == $page.props.user.current_team_id"
                style="width: 1.25rem; height: 1.25rem; color: rgba(52, 211, 153, 1); "  fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> -->
              <v-list-item-title>{{ team.name }}</v-list-item-title>
            </v-list-item>
          </template>
        </v-list>
      </v-menu>

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

    <!-- Responsive Navigation Menu -->
    <v-navigation-drawer
      v-model="drawer"
      :mini-variant="mini"
      clipped
      app
    >
      <v-list
        nav
        shaped
        dense
      >
        <v-list-item-group>
          <v-list-item class="pt-2 pb-4 pl-0">
            <v-list-item-avatar>
              <img
                :src="$page.props.user.profile_photo_url"
                :alt="$page.props.user.name"
              >
            </v-list-item-avatar>

            <v-list-item-content>
              <div>{{ $page.props.user.name }}</div>
              <div class="text--secondary">
                {{ $page.props.user.email }}
              </div>
            </v-list-item-content>
          </v-list-item>

          <inertia-link
            as="v-list-item"
            :href="route('dashboard')"
            :active="route().current('dashboard')"
          >
            <v-list-item-icon>
              <v-icon>mdi-view-dashboard</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Dashboard</v-list-item-title>
          </inertia-link>

          <!-- API Management -->
          <inertia-link
            v-if="$page.props.jetstream.hasApiFeatures"
            as="v-list-item"
            :href="route('api-tokens.index')"
            :active="route().current('api-tokens.index')"
          >
            <v-list-item-icon>
              <v-icon>mdi-api</v-icon>
            </v-list-item-icon>
            <v-list-item-title>API Tokens</v-list-item-title>
          </inertia-link>
          <v-divider />

          <!-- Team Management -->
          <template v-if="$page.props.jetstream.hasTeamFeatures">
            <v-subheader v-if="mini">
              MT
            </v-subheader>
            <v-subheader v-else>
              Manage Team
            </v-subheader>

            <inertia-link
              as="v-list-item"
              :href="route('teams.show', $page.props.user.current_team)"
              :active="route().current('teams.show')"
            >
              <v-list-item-icon>
                <v-icon>mdi-account-multiple-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-title>Team Settings</v-list-item-title>
            </inertia-link>
            <inertia-link
              as="v-list-item"
              :href="route('teams.create')"
              :active="route().current('teams.create')"
            >
              <v-list-item-icon>
                <v-icon>mdi-account-multiple-plus-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-title>Create New Team</v-list-item-title>
            </inertia-link>

            <v-subheader v-if="mini">
              ST
            </v-subheader>
            <v-subheader v-else>
              Switch Teams
            </v-subheader>

            <template v-for="team in $page.props.user.all_teams">
              <v-list-item
                :key="team.id"
                @click.prevent="switchToTeam(team)"
              >
                <!-- <svg v-if="team.id == $page.props.user.current_team_id" class="mr-l" style="width: 1.25rem; height: 1.25rem; color: rgba(52, 211, 153, 1); " fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> -->
                <v-list-item-icon
                  v-if="team.id == $page.props.user.current_team_id"
                >
                  <v-icon style="color: rgba(52, 211, 153, 1); ">
                    mdi-check-circle-outline
                  </v-icon>
                </v-list-item-icon>
                <v-list-item-title>{{ team.name }}</v-list-item-title>
              </v-list-item>
            </template>
            <v-divider />
          </template>

          <v-subheader v-if="mini">
            AC
          </v-subheader>
          <v-subheader v-else>
            Account
          </v-subheader>

          <inertia-link
            as="v-list-item"
            :href="route('profile.show')"
            :active="route().current('profile.show')"
          >
            <v-list-item-icon>
              <v-icon>mdi-account</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Profile</v-list-item-title>
          </inertia-link>

          <v-list-item @click.prevent="logout">
            <v-list-item-icon>
              <v-icon>mdi-exit-run</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Logout</v-list-item-title>
          </v-list-item>
        </v-list-item-group>

        <v-divider />
        <div class="text-center">
          <v-btn
            icon
            large
            @click="mini = !mini"
          >
            <v-icon v-if="mini">
              mdi-chevron-right
            </v-icon>
            <v-icon v-else>
              mdi-chevron-left
            </v-icon>
          </v-btn>
        </div>
        <!-- <v-list-item class="text-center" @click.prevent="mini = !mini">
          <v-list-item-icon>
            <v-icon v-if="mini">mdi-chevron-right</v-icon>
            <v-icon v-else>mdi-chevron-left</v-icon>
          </v-list-item-icon>
        </v-list-item> -->
      </v-list>
    </v-navigation-drawer>

    <v-main>
      <v-container class="pt-7">
        <header
          v-if="$slots.header"
          id="pageheader"
        >
          <slot name="header" />
        </header>

        <slot />
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import HomeSettings from './Settings/Index.vue'

export default {
  components: {
    HomeSettings
  },

  data () {
    return {
      drawer: true,
      mini: false,
      showingNavigationDropdown: false
    }
  },

  methods: {
    switchToTeam (team) {
      this.$inertia.put(
        this.route('current-team.update'),
        {
          team_id: team.id
        },
        {
          preserveState: false
        }
      )
    },

    logout () {
      this.$inertia.post(this.route('logout'))
    }
  }
}
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
.v-list-item:active {
  background: #ccc;
}
</style>
