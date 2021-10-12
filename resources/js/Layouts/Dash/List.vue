<template>
  <v-list
    expand
    nav
    dense
    v-bind="$attrs"
    v-on="$listeners"
  >
    <slot name="user" />
    <template v-for="(item, i) in items">
      <v-subheader
        v-if="item.header"
        :key="`subhead-${i}`"
        inset
        v-text="item.header"
      />
      <v-divider
        v-else-if="item.divider"
        :key="`divider-${i}`"
        :inset="item.inset"
      />
      <list-group
        v-else-if="item.items"
        :key="`group-${i}`"
        :item="item"
      />

      <list-item
        v-else
        :key="`item-${i}`"
        :item="item"
      />
    </template>
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
          <v-list-item-icon v-if="team.id == $page.props.user.current_team_id">
            <v-icon style="color: rgba(52, 211, 153, 1); ">
              mdi-check-circle-outline
            </v-icon>
          </v-list-item-icon>
          <v-list-item-title>{{ team.name }}</v-list-item-title>
        </v-list-item>
      </template>
      <v-divider />
    </template>
    <v-divider />
    <slot name="extra" />
  </v-list>
</template>

<script>
export default {
  name: 'DefaultList',

  components: {
    ListGroup: () => import('./ListGroup'),
    ListItem: () => import('./ListItem')
  },

  props: {
    items: {
      type: Array,
      default: () => []
    }
  }
}
</script>
