<template>
  <web-layout>
    <v-row>
      <v-col
        :class="{'px-0' : $vuetify.breakpoint.smAndDown}"
        cols="12"
        md="9"
        order-md="2"
      >
        <latest-album :albums="albums" />
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
import LatestAlbum from './HomeSections/LatestAlbum.vue'
export default {
  components: { WebLayout, LatestAlbum },
  data: () => ({
    albums: [
      { title: 'Is the order a rabbit? BALLADE SONG ALBUM: Blend of Letters', cover: 'https://media.vgm.io/albums/51/100315/100315-e7c38585b311.jpg', heart: 25, date_created: '30 Dec 2020' },
      { title: 'Is the order a rabbit? Main Theme Rearrange & High Resolution Collection', cover: 'https://media.vgm.io/albums/05/112150/112150-6d73daf0c83b.jpg', heart: 123, date_created: '30 Dec 2020' },
      { title: 'Is the order a rabbit? 10th Anniversary Character Song', cover: 'https://media.vgm.io/albums/94/112149/112149-dda2d5ab9fb8.jpg', heart: 30, date_created: '25 Jan 2022' },
      { title: 'Is the order a rabbit? BALLADE SONG ALBUM: Blend of Letters', cover: 'https://media.vgm.io/albums/51/100315/100315-e7c38585b311.jpg', heart: 25, date_created: '30 Dec 2020' },
      { title: 'Is the order a rabbit? Main Theme Rearrange & High Resolution Collection', cover: 'https://media.vgm.io/albums/05/112150/112150-6d73daf0c83b.jpg', heart: 123, date_created: '30 Dec 2020' },
      { title: 'Is the order a rabbit? 10th Anniversary Character Song', cover: 'https://media.vgm.io/albums/94/112149/112149-dda2d5ab9fb8.jpg', heart: 30, date_created: '25 Jan 2022' }
    ]
  })
}
</script>
