<template>
  <dash-layout>
    <confirm-dialog ref="confirm" />
    <template #header>
      <h3>Artist Listing</h3>
    </template>
    <div>
      <v-card>
        <v-toolbar flat>
          <v-btn
            to="/dashboard/artist/create"
            color="primary"
          >
            <v-icon left>
              mdi-plus
            </v-icon>
            Add
          </v-btn>
        </v-toolbar>
        <v-data-table
          :items="artist"
          :headers="headers"
        >
          <template #[`item.actions`]="{ item }">
            <v-btn
              small
              icon
              color="info"
              :to="'/dashboard/artist/' + item.id + '/edit'"
            >
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
            <v-btn
              small
              icon
              color="error"
              @click="destroy(item.id)"
            >
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </template>
        </v-data-table>
      </v-card>
    </div>
  </dash-layout>
</template>

<script>
import ConfirmDialog from '@/Components/ConfirmDialog.vue'
import DashLayout from '@/Layouts/Dash/Index.vue'
import { VBtn } from 'vuetify/lib'

export default {
  name: 'DashboardAlbum',
  components: {
    DashLayout,
    ConfirmDialog,
    // eslint-disable-next-line vue/no-unused-components
    VBtn
  },
  props: {
    artist: {
      type: Array,
      default: null
    }
  },
  data: () => ({
    dialog: false,
    tanggal: false,
    date: '',
    headers: [
      {
        text: 'ID',
        value: 'id'
      },
      { text: 'Name', value: 'name' },
      { text: 'Date', value: 'updated_at' },
      { text: 'Actions', value: 'actions' }
    ]
  }),
  methods: {
    destroy (id) {
      // confirm
      this.$refs.confirm
        .open('Delete', 'Are you sure to delete this item?', { color: 'red' })
        .then(() => this.$inertia.delete('/dashboard/artist/' + id))
    }
  }
}
</script>
