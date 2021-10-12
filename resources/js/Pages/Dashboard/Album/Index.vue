<template>
  <dash-layout>
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
        <v-data-table />
      </v-card>
    </div>
  </dash-layout>
</template>

<script>
import DashLayout from '@/Layouts/Dash/Index.vue'
import { VBtn } from 'vuetify/lib'

export default {
  name: 'DashboardAlbum',
  components: {
    DashLayout,
    // eslint-disable-next-line vue/no-unused-components
    VBtn
  },
  data: () => ({ dialog: false, tanggal: false, date: '' })
}
</script>
