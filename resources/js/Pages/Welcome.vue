<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import Layout from '@/Layouts/AppLayout.vue'

defineProps<{
  canLogin?: boolean
  canRegister?: boolean
  laravelVersion: string
  phpVersion: string
}>()

</script>

<template>
  <Head title="Welcome" />
  <Layout>
    <v-container>
      <v-row>
        <v-col
          cols="12"
          md="8"
        >
          <section
            v-for="i in 3"
            :key="i"
          >
            <v-container class="pa-md-12">
              <h2 class="text-h4 mb-4">
                Section Heading
              </h2>
              <p>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. A adipisci aliquid aut cum deserunt, eaque enim
                esse et fugit id natus nisi non officiis quaerat, quia quo similique sit unde.
              </p>
            </v-container>
          </section>
        </v-col>
        <v-divider vertical />
        <v-col>
          <section>
            <v-container class="pa-md-12">
              <h2 class="text-h4 mb-4">
                Section Heading
              </h2>
              <p>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. A adipisci aliquid aut cum deserunt, eaque enim
                esse et fugit id natus nisi non officiis quaerat, quia quo similique sit unde.
              </p>
            </v-container>
          </section>
        </v-col>
      </v-row>
    </v-container>
  </Layout>
</template>
