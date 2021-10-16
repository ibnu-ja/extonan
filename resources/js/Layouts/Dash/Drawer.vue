<template>
  <!-- Responsive Navigation Menu -->
  <v-navigation-drawer
    v-model="drawer"
    :mini-variant="mini"
    clipped
    app
  >
    <item-list :items="items">
      <template #user>
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
      </template>
      <template #extra>
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
        <v-list-item @click.prevent="logout">
          <v-list-item-icon>
            <v-icon>mdi-exit-run</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Logout</v-list-item-title>
        </v-list-item>
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
      </template>
    </item-list>
  </v-navigation-drawer>
</template>

<script>
// Utilities
import { sync } from 'vuex-pathify'
export default {
  components: {
    ItemList: () =>
      import(
        /* webpackChunkName: "dashboard-list" */
        './List'
      )
  },
  data: () => ({
    items: [
      {
        title: 'Dashboard',
        icon: 'mdi-view-dashboard',
        to: '/dashboard',
        exact: true
      },
      {
        title: 'Album',
        icon: 'mdi-album',
        to: '/dashboard/album'
      },
      {
        title: 'Artist',
        icon: 'mdi-account-music',
        to: '/dashboard/artist'
      },
      { divider: true, inset: false },
      { header: 'Account', mini: 'AC' },
      {
        title: 'User Profile',
        icon: 'mdi-account',
        to: '/user/profile'
      }
    ]
  }),
  computed: {
    ...sync('dashboard', ['drawer', 'mini'])
  }
}
</script>
