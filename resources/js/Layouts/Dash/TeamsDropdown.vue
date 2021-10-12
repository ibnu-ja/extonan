<template>
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
          <v-list-item-icon v-if="team.id == $page.props.user.current_team_id">
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
</template>
<script>
export default {
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
    }
  }
}
</script>
