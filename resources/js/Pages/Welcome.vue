<template>
  <web-layout>
    <v-row>
      <v-col
        :class="{'px-0' : $vuetify.breakpoint.smAndDown}"
        cols="12"
        md="9"
        order-md="2"
      >
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
                    v-for="card in cards"
                    :key="card.title"
                    :md="card.flex"
                    cols="6"
                  >
                    <v-card :to="'/dashboard'">
                      <v-img
                        :src="card.src"
                        class="white--text align-end"
                        gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                        height="200px"
                      >
                        <v-card-title v-text="card.title" />
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
                            <span class="subheading">24 Jan 2022</span>
                            <v-spacer />
                            <v-icon class="mr-1">
                              mdi-heart
                            </v-icon>
                            <span class="subheading">256</span>
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
      </v-col>
      <v-col
        :class="{'px-0' : $vuetify.breakpoint.smAndDown}"
        cols="12"
        md="3"
        order-md="1"
      >
        <v-row>
          <v-col cols="12">
            <v-card :tile="$vuetify.breakpoint.smAndDown">
              <v-toolbar
                flat
                dense
              >
                <v-toolbar-title><h4>Popular Artists</h4></v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                aaa
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="12">
            <v-card :tile="$vuetify.breakpoint.smAndDown">
              <v-toolbar
                flat
                dense
              >
                <v-toolbar-title><h4>Newest Productssss</h4></v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                aaa
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="12">
            <v-card :tile="$vuetify.breakpoint.smAndDown">
              <v-toolbar
                flat
                dense
              >
                <v-toolbar-title><h4>Newest Orgs</h4></v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                aaa
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </web-layout>
</template>

<script>
import WebLayout from '@/Layouts/Web/Index.vue'
export default {
  components: { WebLayout },
  data: () => ({
    cards: [
      { title: 'Is the order a rabbit? BALLADE SONG ALBUM: Blend of Letters', src: 'https://media.vgm.io/albums/51/100315/100315-e7c38585b311.jpg', flex: 4 },
      { title: 'Is the order a rabbit? Main Theme Rearrange & High Resolution Collection', src: 'https://media.vgm.io/albums/05/112150/112150-6d73daf0c83b.jpg', flex: 4 },
      { title: 'Is the order a rabbit? 10th Anniversary Character Song', src: 'https://media.vgm.io/albums/94/112149/112149-dda2d5ab9fb8.jpg', flex: 4 }
    ]
  })
}
</script>
