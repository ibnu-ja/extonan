<template>
  <section id="popular-artists">
    <v-card :tile="$vuetify.breakpoint.smAndDown">
      <v-img
        style="text-decoration: none;"
        height="180px"
        class="white--text align-end"
        gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
        :src="artists[0].image"
      >
        <inertia-link
          href="/dashboard"
          style="text-decoration: none;"
        >
          <v-app-bar
            flat
            dense
            color="rgba(0, 0, 0, 0)"
          >
            <v-toolbar-title class="text-h6 white--text pl-0">
              Popular Artists
            </v-toolbar-title>
          </v-app-bar>

          <v-card-title class="white--text mt-8">
            <p
              class="ml-3"
              v-text="artists[0].name"
            />
          </v-card-title>
        </inertia-link>
      </v-img>
      <v-list-item
        v-for="(artist) in unpopular"
        :key="artist.name"
        to="/dashboard"
      >
        <v-list-item-avatar>
          <v-img
            :alt="`${artist.name} avatar`"
            :src="artist.image"
          />
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title v-text="artist.name" />
        </v-list-item-content>
      </v-list-item>
    </v-card>
  </section>
</template>

<script>
export default {
  name: 'PopularArtists',
  // eslint-disable-next-line vue/require-prop-types
  props: ['artists'],
  computed: {
    unpopular () {
      // shift first el
      return this.artists.slice(1, this.artists.length)
    }
  }
}
</script>
