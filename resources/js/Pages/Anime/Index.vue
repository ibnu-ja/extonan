<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import Layout from '@/Layouts/AppLayout.vue'
</script>

<template>
  <Head title="Anime" />
  <Layout>
    <v-container>
      <v-row>
        <v-col
          cols="12"
          md="8"
        >
          <section>
            <v-container class="pa-md-12">
              <h2 class="text-h4 mb-4">
                carousel current season
              </h2>
              <v-carousel
                height="200"
                hide-delimiters
                show-arrows="hover"
                cycle
              >
                <v-carousel-item
                  src="https://cdn.vuetifyjs.com/images/cards/docks.jpg"
                  cover
                />

                <v-carousel-item
                  src="https://cdn.vuetifyjs.com/images/cards/hotel.jpg"
                  cover
                />

                <v-carousel-item
                  src="https://cdn.vuetifyjs.com/images/cards/sunshine.jpg"
                  cover
                />
              </v-carousel>
            </v-container>
          </section>
          <section>
            <v-container class="pa-md-12">
              <h2 class="text-h4 mb-4">
                Latest Anime
              </h2>
              <p>
                anime card
              </p>
              <p>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. A accusamus ad aspernatur culpa, delectus
                error explicabo fuga in ipsa, iusto laboriosam natus neque nisi numquam quasi recusandae repellendus,
                sint temporibus!
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
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. A adipisci aliquid aut cum deserunt, eaque
                enim
                esse et fugit id natus nisi non officiis quaerat, quia quo similique sit unde.
              </p>
            </v-container>
          </section>
        </v-col>
      </v-row>
    </v-container>
  </Layout>
</template>
