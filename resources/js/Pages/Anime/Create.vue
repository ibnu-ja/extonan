<script setup lang="ts">

import Layout from '@/Layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { mdiChevronDown, mdiChevronUp, mdiTranslate } from '@mdi/js'

type Languages = 'en' | 'id' | 'jp'

type LanguageItem = {
  label: string
  value: Languages
}

type TranslatableField = {
  [key in Languages]?: string | null;
}

const languages: LanguageItem[] = [
  {
    label: 'English',
    value: 'en',
  }, {
    label: 'Indonesia',
    value: 'id',
  }, {
    label: 'Japanese',
    value: 'jp',
  },
]

type AnimeForm = {
  title: TranslatableField
  description: TranslatableField
}

const form = useForm<AnimeForm>({
  title: {
    en: null,
    id: null,
    jp: null,
  },
  description: {
    en: null,
    id: null,
    jp: null,
  },
},
)

const currentLang = ref<LanguageItem>(languages[0])

const selectLang = (e: unknown) => {
  const lang = e as LanguageItem[]
  if (lang[0]) currentLang.value = lang[0]
}

// asd
</script>

<template>
  <Head title="Create Anime" />
  <Layout>
    <template #header>
      <h1 class="text-h4 text-md-h3">
        Create Anime
      </h1>
    </template>
    <v-container>
      <v-row>
        <v-col
          cols="12"
          md="8"
        >
          <section>
            <v-container class="px-0">
              <v-card>
                <v-card-title class="d-flex">
                  Basic Information
                  <v-spacer />
                  <v-menu>
                    <template #activator="{ props }">
                      <v-btn
                        density="comfortable"
                        color="primary"
                        v-bind="props"
                        :prepend-icon="mdiTranslate"
                        :append-icon="props.isActive ? mdiChevronUp : mdiChevronDown"
                        :text="currentLang?.label"
                      />
                    </template>
                    <v-list
                      return-object
                      mandatory
                      :multiple="false"
                      :items="languages"
                      item-title="label"
                      item-value="value"
                      @update:selected="selectLang"
                    />
                  </v-menu>
                </v-card-title>
                <v-card-text>
                  <!-- template -->
                  <!--<v-text-field-->
                  <!--  label="label"-->
                  <!--  placeholder="placeholder"-->
                  <!--  variant="outlined"-->
                  <!--  hide-details="auto"-->
                  <!--  class="mb-4"-->
                  <!--/>-->
                  <v-text-field
                    v-model="form.title[currentLang.value]"
                    label="Title"
                    variant="outlined"
                    hide-details="auto"
                    class="mb-4"
                  />
                  <v-textarea
                    v-model="form.description[currentLang.value]"
                    label="Description"
                    variant="outlined"
                    hide-details="auto"
                    class="mb-4"
                  />
                </v-card-text>
              </v-card>
              <!--              <h3 class="text-h4 mb-4">-->
              <!--                Latest Anime-->
              <!--              </h3>-->
            </v-container>
          </section>
          <section>
            <v-container class="px-0 py-md-12">
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
