<template>
  <section id="latest-blog">
    <v-row>
      <v-col cols="12">
        <v-card :tile="$vuetify.breakpoint.smAndDown">
          <v-toolbar
            flat
            dense
          >
            <v-toolbar-title><h4>Latest Blog</h4></v-toolbar-title>
            <v-spacer />
            <v-btn small>
              view all
            </v-btn>
          </v-toolbar>
          <v-card-text>
            <v-row dense>
              <v-col
                v-for="album in 10"
                :key="album"
                md="4"
                cols="6"
              >
                <v-card
                  class="mx-auto"
                  max-width="400"
                >
                  <v-img
                    class="white--text align-end"
                    height="200px"
                    gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                    src="https://cdn.vuetifyjs.com/images/cards/docks.jpg"
                  >
                    <v-card-title>Top 10 Australian beaches</v-card-title>
                  </v-img>

                  <v-card-text class="text--primary">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Corporis, mollitia? Eveniet eos enim porro et non assumenda in.
                  </v-card-text>

                  <v-card-actions>
                    <v-btn
                      color="orange"
                      text
                    >
                      Read More
                    </v-btn>
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
  name: 'LatestBlog',
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
