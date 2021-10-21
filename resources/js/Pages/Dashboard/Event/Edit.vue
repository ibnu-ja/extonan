<template>
  <dash-layout>
    <template #header>
      <h3 v-text="$page.props.event ? 'Edit Event' : 'New Event'" />
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
            <!-- Event -->
            <v-col cols="12">
              <v-card>
                <v-toolbar
                  flat
                  dense
                >
                  <v-toolbar-title><h4>Event</h4></v-toolbar-title>
                </v-toolbar>
                <v-card-text class="pt-2">
                  <v-row dense>
                    <v-col cols="12">
                      <v-text-field
                        v-model="event.name"
                        label="Name"
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="event.name_real"
                        label="Name (JP)"
                      />
                    </v-col>
                    <v-col
                      cols="12"
                      md="2"
                    >
                      <v-text-field
                        v-model="event.abbr"
                        label="Abbr"
                      />
                    </v-col>
                    <v-col
                      cols="12"
                      md="5"
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
                            v-model="event.held_from"
                            clearable
                            label="Start"
                            prepend-icon="mdi-calendar"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                          />
                        </template>
                        <v-date-picker
                          v-model="event.held_from"
                          @input="menu = false"
                        />
                      </v-menu>
                    </v-col>
                    <v-col
                      cols="12"
                      md="5"
                    >
                      <v-menu
                        v-model="menu2"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                      >
                        <template #activator="{ on, attrs }">
                          <v-text-field
                            v-model="event.held_from"
                            clearable
                            label="End"
                            prepend-icon="mdi-calendar"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                          />
                        </template>
                        <v-date-picker
                          v-model="event.held_to"
                          @input="menu2 = false"
                        />
                      </v-menu>
                    </v-col><v-col cols="12">
                      <v-textarea
                        v-model="event.desc"
                        label="Description"
                      />
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
                  <!-- TODO Meta event -->
                  TBA
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
                        label="Event ID"
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
  name: 'DashboardEvent',
  components: {
    DashLayout,
    // eslint-disable-next-line vue/no-unused-components
    VBtn,
    ValidationErrors
  },
  data: () => ({
    vgmdb_id: '',
    menu: false,
    menu2: false,
    date: '',
    track_lang: [''],
    event: {
      name: '',
      name_real: '',
      abbr: '',
      desc: '',
      held_from: '',
      held_to: '',
      meta: {}
    }
  }),
  computed: {
    hasErrors () {
      return Object.keys(this.$page.props.errors).length > 0
    }
  },
  created () {
    if (this.$page.props.event) {
      this.event = this.$page.props.event
    }
  },
  methods: {
    test () {
      // TODO: rename method
      // TODO: add loader
      this.axios
        .get('https://vgmdb.info/event/' + this.vgmdb_id + '?format=json')
        .then(res => {
          this.event.name = res.data.name
          // this.event.name_real = res.data.name_real
          this.event.held_to = res.data.enddate
          this.event.held_from = res.data.startdate
          this.event.desc = res.data.notes
        })
        .catch(err => console.log(err.data))
    },
    submit () {
      // const data = function () {
      //   return this.artist.filter(item => item !== null)
      // }
      if (this.$page.props.event) {
        this.$inertia.put('/dashboard/event/' + this.event.id, this.event)
      } else this.$inertia.post('/dashboard/event', this.event)
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
