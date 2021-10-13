<template>
  <dash-layout>
    <confirm-dialog ref="confirm" />
    <template #header>
      <h3>Album</h3>
    </template>
    <div>
      <v-card>
        <v-toolbar flat>
          <inertia-link
            href="/dashboard/album/create"
            as="v-btn"
            color="primary"
          >
            Tambah
          </inertia-link>
        </v-toolbar>
        <v-data-table
          :items="album"
          :headers="headers"
        >
          <template #[`item.actions`]="{ item }">
            <v-btn
              small
              icon
              color="info"
              :to="'/dashboard/album/' + item.id + '/edit'"
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
    album: {
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
        .then(() => this.$inertia.delete('/dashboard/album/' + id))
    }
  }
}
</script>
