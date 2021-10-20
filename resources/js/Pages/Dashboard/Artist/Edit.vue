<template>
  <dash-layout>
    <template #header>
      <h3 v-text="$page.props.artist ? 'Edit Artist' : 'New Artist'" />
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
            <!-- Artist -->
            <v-col cols="12">
              <v-card>
                <v-toolbar
                  flat
                  dense
                >
                  <v-toolbar-title><h4>Artist</h4></v-toolbar-title>
                </v-toolbar>
                <v-card-text class="pt-2">
                  <v-row dense>
                    <v-col cols="12">
                      <v-text-field
                        v-model="artist.name"
                        label="Name"
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="artist.name_real"
                        label="Name (Kanji)"
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="artist.name_trans"
                        label="Name (Kana)"
                      />
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <!-- TODO gender combobox -->
                      <v-text-field
                        v-model="artist.sex"
                        label="Gender"
                      />
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-text-field
                        v-model="artist.birthplace"
                        label="Birthplace"
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
                            v-model="artist.birthdate"
                            clearable
                            label="Release Date"
                            prepend-icon="mdi-calendar"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                          />
                        </template>
                        <v-date-picker
                          v-model="artist.birthdate"
                          @input="menu = false"
                        />
                      </v-menu>
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-col>
            <!-- Meta -->
            <v-col cols="12">
              <v-card>
                <v-toolbar
                  flat
                  dense
                >
                  <v-toolbar-title><h4>Meta info</h4></v-toolbar-title>
                </v-toolbar>
                <v-card-text class="pt-2">
                  <!-- TODO Meta web -->
                  TBA
                  <!-- <v-row dense>
                    <v-col cols="12">
                      <v-text-field
                        v-model="artist.name"
                        label="Name"
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="artist.name_real"
                        label="Name (Kanji)"
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="artist.name_trans"
                        label="Name (Kana)"
                      />
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-text-field
                        v-model="artist.sex"
                        label="Gender"
                      />
                    </v-col>
                    <v-col
                      cols="12"
                      md="4"
                    >
                      <v-text-field
                        v-model="artist.birthplace"
                        label="Birthplace"
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
                            v-model="artist.birthdate"
                            label="Release Date"
                            prepend-icon="mdi-calendar"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                          />
                        </template>
                        <v-date-picker
                          v-model="artist.birthdate"
                          @input="menu = false"
                        />
                      </v-menu>
                    </v-col>
                  </v-row> -->
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
                <v-card-text class="pt-2">
                  <v-row no-gutters>
                    <v-col cols="12">
                      <v-text-field
                        v-model="vgmdb_id"
                        label="Artist ID"
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
                <v-toolbar
                  flat
                  dense
                >
                  <v-toolbar-title> <h4>Publication</h4></v-toolbar-title>
                </v-toolbar>
                <v-card-text class="pt-2">
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
  name: 'DashboardArtist',
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
    artist: {
      birthdate: '',
      birthplace: '',
      name: '',
      name_real: '',
      name_trans: '',
      sex: '',
      meta: {
        twitter: '',
        website: ''
      }
    }
  }),
  computed: {
    hasErrors () {
      return Object.keys(this.$page.props.errors).length > 0
    }
  },
  created () {
    if (this.$page.props.artist) {
      this.artist = this.$page.props.artist
    }
  },
  methods: {
    test () {
      // TODO: rename method
      // TODO: add loader
      this.axios
        .get('https://vgmdb.info/artist/' + this.vgmdb_id + '?format=json')
        .then(res => {
          this.artist.name = res.data.name
          this.artist.name_real = res.data.name_real
          this.artist.name_trans = res.data.name_trans
          this.artist.birthdate = res.data.birthdate
          this.artist.birthplace = res.data.birthplace
          this.artist.sex = res.data.sex
        })
        .catch(err => console.log(err.data))
    },
    submit () {
      // const data = function () {
      //   return this.artist.filter(item => item !== null)
      // }
      if (this.$page.props.artist) {
        this.$inertia.put('/dashboard/artist/' + this.artist.id, this.artist)
      } else this.$inertia.post('/dashboard/artist', this.artist)
    },
    splice (array, index) {
      array.splice(index, 1)
    },
    push (array) {
      array.push('')
    }
  }
}
</script>
