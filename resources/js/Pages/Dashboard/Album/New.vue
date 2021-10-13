<template>
  <dash-layout>
    <template #header>
      <h3>New Album</h3>
    </template>
    <form @submit.prevent="submit">
      <v-row>
        <v-col
          cols="12"
          md="8"
        >
          <v-row>
            <v-col
              v-if="hasErrors"
              cols="12"
            >
              <validation-errors />
            </v-col>
            <!-- Album -->
            <v-col cols="12">
              <v-card>
                <v-toolbar flat>
                  <v-toolbar-title><h4>Album</h4></v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                  <v-row dense>
                    <v-col cols="12">
                      <v-text-field
                        v-model="album.name"
                        label="Display Title"
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="album.name_real"
                        label="Original/JP Title"
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="album.name_trans"
                        label="Romaji Title"
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field label="Other Title" />
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-text-field
                        v-model="album.barcode"
                        label="Barcode"
                      />
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-text-field
                        v-model="album.catalog"
                        label="Catalog"
                      />
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-menu
                        v-model="menu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                      >
                        <template #activator="{ on, attrs }">
                          <v-text-field
                            v-model="album.release_date"
                            label="Release Date"
                            prepend-icon="mdi-calendar"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                          />
                        </template>
                        <v-date-picker
                          v-model="album.release_date"
                          @input="menu = false"
                        />
                      </v-menu>
                    </v-col>
                    <v-col cols="12">
                      <v-textarea
                        v-model="album.desc"
                        label="Deskripsi"
                      />
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-col>
            <!-- Disc Type -->
            <v-col cols="12">
              <v-card>
                <v-toolbar
                  flat
                  dense
                >
                  <v-toolbar-title>
                    <h4>Tracks Title Language</h4>
                  </v-toolbar-title>
                  <v-spacer />
                  <v-btn @click="push(track_lang)">
                    Add
                  </v-btn>
                </v-toolbar>
                <v-card-text>
                  <v-row dense>
                    <v-col
                      v-for="(lang, index) in track_lang"
                      :key="index"
                    >
                      <v-text-field
                        v-model="track_lang[index]"
                        :label="'Lang #' + (index + 1)"
                        append-icon="mdi-close"
                        @click:append="splice(track_lang, index)"
                      />
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-col>
            <!-- Disc -->
            <v-col cols="12">
              <v-card>
                <v-toolbar
                  flat
                  dense
                  class="mb-2"
                >
                  <v-toolbar-title><h4>Discs</h4></v-toolbar-title>
                  <v-spacer />

                  <v-btn @click="addDisc()">
                    Add Disc
                  </v-btn>
                </v-toolbar>
                <v-row no-gutters>
                  <v-col cols="12">
                    <v-card
                      v-for="(disc, index) in album.discs"
                      :key="index"
                      class="ma-2 pa-2"
                      outlined
                    >
                      <v-toolbar
                        flat
                        dense
                      >
                        <v-toolbar-title>
                          <h5>Disc #{{ index + 1 }}</h5>
                        </v-toolbar-title>
                        <v-spacer />
                        <v-btn
                          small
                          icon
                          @click="splice(album.discs, i)"
                        >
                          <v-icon>mdi-delete</v-icon>
                        </v-btn>
                      </v-toolbar>
                      <v-row dense>
                        <v-col cols="12">
                          <v-row dense>
                            <v-col cols="8">
                              <v-text-field
                                v-model="disc.name"
                                label="Disc Name"
                              />
                            </v-col>
                            <v-col cols="4">
                              <v-text-field
                                v-model="disc.disc_length"
                                label="Disc Length"
                              />
                            </v-col>
                          </v-row>
                        </v-col>
                        <v-col
                          v-for="(track, i) in disc.tracks"
                          :key="i"
                          cols="12"
                        >
                          <v-card outlined>
                            <v-toolbar
                              flat
                              dense
                              max-height="30px"
                              class="px-2"
                            >
                              <h5>Track #{{ i + 1 }}</h5>
                              <!-- <v-toolbar-title>
                        </v-toolbar-title> -->
                              <v-spacer />
                              <v-btn
                                small
                                icon
                                @click="splice(disc.tracks, i)"
                              >
                                <v-icon>mdi-delete</v-icon>
                              </v-btn>
                            </v-toolbar>
                            <v-row
                              dense
                              class="pa-2"
                            >
                              <v-col
                                cols="12"
                                md="4"
                              >
                                <v-text-field
                                  v-model="track.track_length"
                                  :disabled="
                                    Object.keys(track.names).length === 0
                                  "
                                  label="Track Length"
                                />
                              </v-col>
                              <v-col
                                v-for="(lang, j) in track_lang"
                                :key="j"
                                :order="j === 0 ? 'first' : ''"
                                cols="12"
                                md="8"
                              >
                                <v-text-field
                                  v-model="track.names[lang]"
                                  :disabled="!lang"
                                  :label="'Title ' + lang"
                                />
                              </v-col>
                            </v-row>
                          </v-card>
                        </v-col>
                        <v-col
                          cols="12"
                          class="px-0"
                        >
                          <v-btn @click="addTrack(disc)">
                            add track
                          </v-btn>
                        </v-col>
                      </v-row>
                    </v-card>
                  </v-col>
                </v-row>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
        <v-col
          cols="12"
          md="4"
        >
          <v-row>
            <v-col cols="12">
              <v-card>
                <v-toolbar
                  dense
                  flat
                  max-height="30px"
                >
                  <v-toolbar-title>
                    <h4>Get data from VGMDB</h4>
                  </v-toolbar-title>
                </v-toolbar>
                <v-card-text class="normal--text">
                  <v-row no-gutters>
                    <v-col cols="12">
                      <v-text-field
                        v-model="vgmdb_id"
                        label="Album ID"
                      />
                    </v-col>
                    <!-- TODO preview function -->
                    <v-col cols="12">
                      <v-btn @click="test">
                        submit
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="12">
              <v-card>
                <v-toolbar flat>
                  <v-toolbar-title> <h4>Publication</h4></v-toolbar-title>
                </v-toolbar>
                <v-card-text class="normal--text">
                  <v-row no-gutters>
                    <v-col cols="6">
                      <v-btn type="submit">
                        Save
                      </v-btn>
                    </v-col>
                    <!-- TODO preview function -->
                    <v-col cols="6">
                      <v-btn> Preview (TBA)</v-btn>
                    </v-col>
                  </v-row>
                  di sini ngurus visibility tanggal publish dsb
                  <v-list dense>
                    <v-list-item class="py-0">
                      <v-list-item-icon class="mx-3">
                        <v-icon>mdi-eye</v-icon>
                      </v-list-item-icon>
                      <v-list-item-content>
                        aa
                      </v-list-item-content>
                    </v-list-item>
                  </v-list>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </form>
  </dash-layout>
</template>

<script>
import DashLayout from '@/Layouts/Dash/Index.vue'
import { VBtn } from 'vuetify/lib'
import ValidationErrors from '@/Components/ValidationErrors.vue'

export default {
  name: 'DashboardAlbum',
  components: {
    DashLayout,
    // eslint-disable-next-line vue/no-unused-components
    VBtn,
    ValidationErrors
  },
  data: () => ({
    vgmdb_id: '',
    menu: false,
    date: '',
    track_lang: [''],
    album: {
      name: '',
      name_real: '',
      name_trans: '',
      barcode: '',
      catalog: '',
      release_date: '',
      desc: '',
      discs: [
        {
          disc_length: '',
          name: '',
          tracks: [
            {
              names: {},
              track_length: ''
            }
          ]
        }
      ],
      media_format: ''
    }
  }),
  computed: {
    hasErrors () {
      return Object.keys(this.$page.props.errors).length > 0
    }
  },
  methods: {
    test () {
      this.axios
        .get('https://vgmdb.info/album/' + this.vgmdb_id + '?format=json')
        .then(res => {
          console.log(res.data)
          console.log(Object.keys(res.data.discs[0].tracks[0].names))
          this.track_lang = Object.keys(res.data.discs[0].tracks[0].names)
          this.album.discs = res.data.discs
          this.album.name = res.data.name
          this.album.desc = res.data.notes
          this.album.name_real = res.data.names.ja
          this.album.name_trans = res.data.names['ja-latn']
          this.album.barcode = res.data.barcode
          this.album.catalog = res.data.catalog
          this.album.release_date = res.data.release_date
          this.album.media_format = res.data.media_format
        })
        .catch(err => console.log(err.data))
    },
    submit () {
      this.$inertia.post('/dashboard/album', this.album)
    },
    splice (array, index) {
      array.splice(index, 1)
    },
    addDisc () {
      this.album.discs.push({
        disc_length: '',
        name: '',
        tracks: [
          {
            names: {},
            track_length: ''
          }
        ]
      })
    },
    addTrack (disc) {
      disc.tracks.push({
        names: {},
        track_length: ''
      })
    },
    push (array) {
      array.push('')
    }
  }
}
</script>
