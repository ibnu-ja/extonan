<template>
  <dash-layout>
    <template #header>
      <h3 v-text="$page.props.album ? 'Edit Album' : 'New Album'" />
    </template>
    <v-form
      :disabled="loading"
      @submit.prevent="submit"
    >
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
                <v-toolbar
                  flat
                  dense
                >
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
                            clearable
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
            <v-col cols="12">
              <v-card>
                <v-toolbar
                  flat
                  dense
                >
                  <v-toolbar-title><h4>Organizations</h4></v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                  <v-autocomplete
                    v-model="album.orgs.label"
                    multiple
                    clearable
                    chips
                    deletable-chips
                    :items="organizations"
                    item-text="name"
                    item-value="id"
                    label="Label"
                  />
                  <v-autocomplete
                    v-model="album.orgs.publisher"
                    multiple
                    clearable
                    chips
                    deletable-chips
                    :items="organizations"
                    item-text="name"
                    item-value="id"
                    label="Publisher"
                  />
                  <v-autocomplete
                    v-model="album.orgs.distributor"
                    multiple
                    clearable
                    chips
                    deletable-chips
                    :items="organizations"
                    item-text="name"
                    item-value="id"
                    label="Distributor"
                  />
                  <v-autocomplete
                    v-model="album.orgs.manufacturer"
                    multiple
                    clearable
                    chips
                    deletable-chips
                    :items="organizations"
                    item-text="name"
                    item-value="id"
                    label="Manufacturer"
                  />
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="12">
              <v-card>
                <v-toolbar
                  flat
                  dense
                >
                  <v-toolbar-title>
                    <h4>Products</h4>
                  </v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                  <v-autocomplete
                    v-model="album.products"
                    multiple
                    clearable
                    chips
                    deletable-chips
                    :items="products"
                    item-text="name"
                    item-value="id"
                    label="Product Represented"
                  />
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="12">
              <v-card>
                <v-toolbar
                  flat
                  dense
                >
                  <v-toolbar-title><h4>Artist</h4></v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                  <!-- TODO Meta organization -->
                  <v-autocomplete
                    v-model="album.roles.performers"
                    multiple
                    clearable
                    chips
                    deletable-chips
                    :items="artists"
                    item-text="name"
                    item-value="id"
                    label="Performers"
                  />
                  <v-autocomplete
                    v-model="album.roles.composers"
                    multiple
                    clearable
                    chips
                    deletable-chips
                    :items="artists"
                    item-text="name"
                    item-value="id"
                    label="Composers"
                  />
                  <v-autocomplete
                    v-model="album.roles.lyricists"
                    multiple
                    clearable
                    chips
                    deletable-chips
                    :items="artists"
                    item-text="name"
                    item-value="id"
                    label="Lyricists"
                  />
                  <v-autocomplete
                    v-model="album.roles.arrangers"
                    multiple
                    clearable
                    chips
                    deletable-chips
                    :items="artists"
                    item-text="name"
                    item-value="id"
                    label="Arrangers"
                  />
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
                >
                  <v-toolbar-title><h4>Discs</h4></v-toolbar-title>
                  <v-spacer />

                  <v-btn @click="addDisc()">
                    Add Disc
                  </v-btn>
                </v-toolbar>
                <v-card-text class="pa-2">
                  <v-card
                    v-for="(disc, index) in album.discs"
                    :key="index"
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
                        :title="'Delete Disc ' + (index + 1)"
                        @click="splice(album.discs, i)"
                      >
                        <v-icon>mdi-delete</v-icon>
                      </v-btn>
                    </v-toolbar>
                    <v-card-text class="pa-2">
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
                        <v-col
                          v-for="(track, i) in disc.tracks"
                          :key="i"
                          cols="12"
                        >
                          <v-card outlined>
                            <v-toolbar
                              flat
                              dense
                            >
                              <v-toolbar-title>
                                <h5>Track #{{ i + 1 }}</h5>
                              </v-toolbar-title>
                              <v-spacer />
                              <v-btn
                                small
                                icon
                                @click="splice(disc.tracks, i)"
                              >
                                <v-icon>mdi-delete</v-icon>
                              </v-btn>
                            </v-toolbar>
                            <v-card-text class="pa-2">
                              <v-row dense>
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
                            </v-card-text>
                          </v-card>
                        </v-col>
                        <v-col cols="12">
                          <v-btn @click="addTrack(disc)">
                            add track
                          </v-btn>
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-card-text>
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
                >
                  <v-toolbar-title>
                    <h4>Get data from VGMDB</h4>
                  </v-toolbar-title>
                </v-toolbar>
                <v-card-text class="normal--text">
                  <v-text-field
                    v-model="vgmdb_id"
                    label="Album ID"
                  />
                </v-card-text>
                <v-card-actions>
                  <v-btn @click="test">
                    submit
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-col>
            <v-col cols="12">
              <image-gallery-upload
                v-model="album.images"
                multiple
                :on-server-files="album.uploadedGallery"
                @remove="onRemove"
              />
            </v-col>
            <v-col cols="12">
              <v-card>
                <v-toolbar
                  dense
                  flat
                >
                  <v-toolbar-title>
                    <h4>Event</h4>
                  </v-toolbar-title>
                </v-toolbar>
                <v-card-text class="normal--text">
                  <v-autocomplete
                    v-model="album.event_id"
                    clearable
                    :items="events"
                    item-text="name"
                    item-value="id"
                    label="Event"
                  />
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="12">
              <v-card>
                <v-toolbar
                  dense
                  flat
                >
                  <v-toolbar-title> <h4>Publication</h4></v-toolbar-title>
                </v-toolbar>
                <v-card-text>
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
                <v-card-actions>
                  <v-btn type="submit">
                    Save
                  </v-btn>
                  <v-spacer />
                  <v-btn> Preview (TBA)</v-btn>
                </v-card-actions>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-form>
  </dash-layout>
</template>

<script>
import DashLayout from '@/Layouts/Dash/Index.vue'
import { VBtn } from 'vuetify/lib'
import ValidationErrors from '@/Components/ValidationErrors.vue'
import ImageGalleryUpload from '@/Components/ImageGalleryUpload.vue'

export default {
  components: {
    DashLayout,
    ImageGalleryUpload,
    // eslint-disable-next-line vue/no-unused-components
    VBtn,
    ValidationErrors
  },
  methods: {

    async getIdsForRepresentedProducts (vgmdb) {
      return await Promise.all(
        vgmdb.products.map(async product => {
          if (product.link) product.link = 'https://vgmdb.net/' + product.link
          const res = await this.axios.post(
            this.route('product.insertVgmdb'),
            product
          )
          return res.data.id
        })
      )
    },
    onRemove (item) {
      console.log(item)
    },
    async getIdsForCreditedArtists (vgmdb) {
      return await Promise.all(
        Object.keys(this.album.roles).map(async role => {
          // return role
          return await Promise.all(
            vgmdb[role].map(async artist => {
              // this.album.roles[role] = []
              if (artist.link) artist.link = 'https://vgmdb.net/' + artist.link
              const res = await this.axios.post(
                this.route('artist.insertVgmdb'),
                artist
              )
              return res.data
            })
          )
        })
      )
    },
    async createEventIfNotExist (vgmdb) {
      const event = vgmdb.release_events[0]
      const dates = event.name.match(
        // eslint-disable-next-line no-useless-escape
        /(?:(((Jan(uary)?|Ma(r(ch)?|y)|Jul(y)?|Aug(ust)?|Oct(ober)?|Dec(ember)?)\ 31)|((Jan(uary)?|Ma(r(ch)?|y)|Apr(il)?|Ju((ly?)|(ne?))|Aug(ust)?|Oct(ober)?|(Sep(t(ember)?)?|Nov|Dec)(ember)?)\ (0?[1-9]|([12]\d)|30))|(Feb(ruary)?\ (0?[1-9]|1\d|2[0-8]|(29(?=,\ ((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)))))))\,\ ((1[6-9]|[2-9]\d)\d{2}))/g
      )
      if (dates[0]) {
        event.held_from = new Date(dates[0]).toISOString().split('T')[0]
      }
      if (dates[1]) {
        event.held_to = new Date(dates[1]).toISOString().split('T')[0]
      }
      delete event.shortname
      if (event.link) event.link = 'https://vgmdb.net/' + event.link
      event.name = event.name.replace(/(Released at )|( \(.*)/g, '')
      return await this.axios.post(this.route('event.insertVgmdb'), event)
    },
    async getIdsForOrganizations (vgmdb) {
      return await Promise.all(
        vgmdb.organizations.map(async org => {
          if (org.link) org.link = 'https://vgmdb.net/' + org.link
          const res = await this.axios.post(
            this.route('organization.insertVgmdb'),
            org
          )
          return res
        })
      )
    },
    async test () {
      // TODO: rename method
      try {
        this.loading = true
        const vgmdb = await this.axios
          .get('https://vgmdb.info/album/' + this.vgmdb_id + '?format=json')
          .then(res => {
            return res.data
          })
        const credit = await this.getIdsForCreditedArtists(vgmdb)
        if (vgmdb.release_events.length > 0) {
          const event = await this.createEventIfNotExist(vgmdb)
          this.events = await this.axios
            .get(this.route('event.indexJson'))
            .then(res => {
              return res.data
            })
          this.album.event_id = event.data.id
        }
        this.artists = await this.axios
          .get(this.route('artist.indexJson'))
          .then(res => {
            return res.data
          })
        Object.keys(this.album.roles).forEach((role, index) => {
          this.album.roles[role] = credit[index].map(el => {
            return el.id
          })
        })

        await this.getIdsForOrganizations(vgmdb)
        // console.log(event)
        this.organizations = await this.axios
          .get(this.route('organization.indexJson'))
          .then(res => {
            return res.data
          })

        const products = await this.getIdsForRepresentedProducts(vgmdb)
        this.products = await this.axios
          .get(this.route('product.indexJson'))
          .then(res => {
            return res.data
          })

        this.album.products = products

        this.track_lang = Object.keys(vgmdb.discs[0].tracks[0].names)
        this.album.discs = vgmdb.discs
        this.album.name = vgmdb.name
        this.album.desc = vgmdb.notes
        this.album.name_real = vgmdb.names.ja
        this.album.name_trans = vgmdb.names['ja-latn']
        this.album.barcode = vgmdb.barcode
        this.album.catalog = vgmdb.catalog
        this.album.release_date = vgmdb.release_date
        this.album.media_format = vgmdb.media_format
      } catch (e) {
        console.error(e)
      } finally {
        this.loading = false
      }
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
