<template>
  <dash-layout>
    <template #header>
      <h3>New Artist</h3>
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
            <!-- Product -->
            <v-col cols="12">
              <v-card>
                <v-toolbar
                  flat
                  dense
                >
                  <v-toolbar-title><h4>Product</h4></v-toolbar-title>
                </v-toolbar>
                <v-card-text class="pt-2">
                  <v-row dense>
                    <v-col cols="12">
                      <v-text-field
                        v-model="product.name"
                        label="Name"
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="product.name_real"
                        label="Name (JP)"
                      />
                    </v-col>
                    <v-col
                      cols="12"
                      md="6"
                    >
                      <v-combobox
                        v-model="product.type"
                        :items="$page.props.type"
                        label="Type"
                      />
                      <!-- <v-text-field
                        v-model="product.type"
                        label="Birthplace"
                      /> -->
                    </v-col>
                    <v-col
                      cols="12"
                      md="6"
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
                            v-model="product.release_date"
                            clearable
                            label="Release Date"
                            prepend-icon="mdi-calendar"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                          />
                        </template>
                        <v-date-picker
                          v-model="product.release_date"
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
                  <!-- TODO Meta product -->
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
                        label="Product ID"
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
  name: 'DashboardProduct',
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
    product: {
      type: '',
      release_date: '',
      name: '',
      name_real: '',
      meta: {}
    }
  }),
  computed: {
    hasErrors () {
      return Object.keys(this.$page.props.errors).length > 0
    }
  },
  created () {
    if (this.$page.props.product) {
      this.product = this.$page.props.product
    }
  },
  methods: {
    test () {
      // TODO: rename method
      // TODO: add loader
      this.axios
        .get('https://vgmdb.info/product/' + this.vgmdb_id + '?format=json')
        .then(res => {
          this.product.name = res.data.name
          this.product.name_real = res.data.name_real
          this.product.type = res.data.type
          this.product.release_date = res.data.release_date
        })
        .catch(err => console.log(err.data))
    },
    submit () {
      // const data = function () {
      //   return this.artist.filter(item => item !== null)
      // }
      if (this.$page.props.product) {
        this.$inertia.put('/dashboard/product/' + this.product.id, this.product)
      } else this.$inertia.post('/dashboard/product', this.product)
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
