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
        /* webpackChunkName: "default-list" */
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
        to: '/dashboard/album',
        exact: true
      },
      { divider: true, inset: false },
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
