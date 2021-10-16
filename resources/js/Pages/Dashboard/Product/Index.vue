<template>
  <dash-layout>
    <confirm-dialog ref="confirm" />
    <template #header>
      <h3>Product Listing</h3>
    </template>
    <div>
      <v-card>
        <v-toolbar flat>
          <v-btn
            :to="route('product.create')"
            color="primary"
          >
            <v-icon left>
              mdi-plus
            </v-icon>
            Add
          </v-btn>
        </v-toolbar>
        <v-data-table
          :items="product"
          :headers="headers"
        >
          <template #[`item.actions`]="{ item }">
            <v-btn
              small
              icon
              color="info"
              :to="'/dashboard/product/' + item.id + '/edit'"
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

export default {
  name: 'DashboardProduct',
  components: {
    DashLayout,
    ConfirmDialog
  },
  props: {
    product: {
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
        .then(() => this.$inertia.delete('/dashboard/product/' + id))
    }
  }
}
</script>
