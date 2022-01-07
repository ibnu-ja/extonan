<template>
  <section id="latest-album">
    <v-row>
      <v-col cols="12">
        <v-card :tile="$vuetify.breakpoint.smAndDown">
          <v-toolbar
            flat
            dense
          >
            <v-toolbar-title><h4>Latest Album</h4></v-toolbar-title>
            <v-spacer />
            <v-btn small>
              view all
            </v-btn>
          </v-toolbar>
          <v-card-text>
            <v-row dense>
              <v-col
                v-for="album in albums"
                :key="album.id"
                lg="3"
                md="4"
                cols="6"
              >
                <v-card :to="'/album/'+album.slug">
                  <v-img
                    :src="album.cover"
                    class="white--text align-end"
                    gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                    height="200px"
                  >
                    <v-card-title v-text="album.title" />
                  </v-img>

                  <v-card-actions>
                    <v-list-item
                      class="grow"
                      dense
                    >
                      <v-row
                        align="center"
                        justify="start"
                      >
                        <v-icon class="mr-1">
                          mdi-calendar
                        </v-icon>
                        <span class="subheading">{{ album.date_created }}</span>
                        <v-spacer />
                        <v-icon class="mr-1">
                          mdi-heart
                        </v-icon>
                        <span class="subheading">{{ album.heart }}</span>
                      </v-row>
                    </v-list-item>
                  </v-card-actions>
                </v-card>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </section>
</template>

<script>
export default {
  name: 'LatestAlbum',
  props: {
    albums: {
      type: Array,
      default: () => [{
        id: 0,
        title: 'N/A',
        cover: '',
        heart: 0,
        date_created: 'N/A'
      }]
    }
  }
}
</script>
