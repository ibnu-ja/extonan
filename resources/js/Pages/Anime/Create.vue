<script setup lang="ts">

import Layout from '@/Layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { useDisplay } from 'vuetify'
import { mdiClose, mdiSend } from '@mdi/js'

type Languages = 'en' | 'id' | 'romaji' | 'native'

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
    label: 'Native',
    value: 'native',
  }, {
    label: 'Romaji',
    value: 'romaji',
  },
]

type AnimeForm = {
  title: TranslatableField
  description: TranslatableField
  anilist_id: number | null
}

const form = useForm<AnimeForm>({
  title: {
    en: null,
    id: null,
    native: null,
    romaji: null,
  },
  description: {
    en: null,
    id: null,
  },
  anilist_id: null,
},
)

const anilistData = ref()
const show = ref(false)

const { smAndUp } = useDisplay()
const currentLang = ref<LanguageItem>(languages[0])
//

const anilistHitApi = async () => {
  if (!form.anilist_id || form.anilist_id < 0) {
    return
  }

  const query = `
    query ($id: Int) {
      Media (id: $id, type: ANIME) {
        id
        episodes
        coverImage {
          extraLarge
          large
          medium
          color
        }
        startDate {
          year
          month
          day
        }
        endDate {
          year
          month
          day
        }
        characters {
          edges { # Array of character edges
            node { # Character node
              id
              name {
                full
              }
              image {
                large
                medium
              }
            }
            role
            voiceActors(language: JAPANESE) { # Array of voice actors of this character for the anime
              id
              image {
                large
                medium
              }
              name {
                full
              }
            }
          }
        }
        seasonYear
        season
        seasonInt
        genres
        title {
          romaji
          english
          native
        }
      }
    }`
  const variables = { id: form.anilist_id }

  try {
    const response = await fetch('https://graphql.anilist.co/', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ query, variables }),
      credentials: 'omit',
      mode: 'cors',
      referrer: 'https://anilist.co/',
    })

    const { data: { Media: anime } } = await response.json()

    console.log(anime)
    const { romaji, english: en, native } = anime.title
    anilistData.value = anime
    form.title = {
      romaji,
      en,
      native,
    }
    show.value = !show.value
  } catch (error) {
    alert(error.message)
    console.error(error)
  }
}

const clearAnilist = () => anilistData.value = null
</script>

<template>
  <Head title="Create Anime" />
  <Layout>
    <template #header>
      <h1 class="text-h4 text-md-h3">
        Create Anime
      </h1>
    </template>
    <v-container class="pa-0 pa-sm-4">
      <v-row>
        <v-col
          cols="12"
          md="8"
        >
          <section class="mb-4">
            <v-card :rounded="smAndUp">
              <v-card-item title="Basic Information" />
              <v-divider />
              <v-card-text>
                <v-text-field
                  v-model="form.title[currentLang.value]"
                  :label="`Title (${currentLang.value})`"
                  variant="outlined"
                  hide-details="auto"
                  class="mb-4"
                >
                  <template
                    #prepend
                  >
                    <v-select
                      v-model="currentLang"
                      :multiple="false"
                      label="Title language"
                      return-object
                      variant="outlined"
                      :items="languages"
                      item-title="label"
                      hide-details
                      item-value="value"
                    />
                  </template>
                </v-text-field>
                <v-textarea
                  v-model="form.description[currentLang.value]"
                  label="Description"
                  variant="outlined"
                  hide-details="auto"
                  class="mb-4"
                />
              </v-card-text>
            </v-card>
          </section>
          <section class="mb-6">
            <v-card :rounded="smAndUp">
              <v-row
                no-gutters
              >
                <v-slide-x-transition>
                  <v-col
                    v-if="anilistData"
                    cols="4"
                  >
                    <v-img
                      :src="anilistData.coverImage.extraLarge"
                      :lazy-src="anilistData.coverImage.medium"
                    />
                  </v-col>
                </v-slide-x-transition>
                <v-col>
                  <v-card-item>
                    <v-card-title class="d-flex">
                      Anilist Metadata
                    </v-card-title>
                  </v-card-item>
                  <v-divider />
                  <v-card-text>
                    <!-- template -->
                    <v-text-field
                      v-model="form.anilist_id"
                      label="Anilist ID"
                      type="number"
                      :error-messages="form.errors.anilist_id"
                      placeholder="placeholder"
                      variant="outlined"
                      hide-details="auto"
                      class="mb-4"
                    >
                      <template #append>
                        <v-icon
                          v-if="anilistData == null"
                          :icon="mdiSend"
                          @click="anilistHitApi"
                        />
                        <v-icon
                          v-else
                          :icon="mdiClose"
                          @click="clearAnilist"
                        />
                      </template>
                    </v-text-field>
                  </v-card-text>
                  <v-expand-transition>
                    <div v-if="anilistData">
                      <v-divider />

                      <v-card-text>
                        <div class="d-flex flex-wrap gap-2">
                          <v-chip
                            v-for="genre in anilistData.genres"
                            :key="genre"
                            class="mb-4"
                          >
                            {{ genre }}
                          </v-chip>
                        </div>
                        <ul class="mb-4 pl-6">
                          <li><strong>Episodes</strong>: {{ anilistData.episodes }}</li>
                          <li><strong>Season</strong>: {{ anilistData.season }} {{ anilistData.seasonYear }}</li>
                          <li v-if="anilistData.startDate">
                            <strong>Start date</strong>: {{ anilistData.startDate }}
                          </li>
                        </ul>
                      </v-card-text>
                    </div>
                  </v-expand-transition>
                </v-col>
              </v-row>
            </v-card>
          </section>

          <v-expand-transition>
            <section
              v-if="anilistData && anilistData.characters?.edges"
              class="mb-6"
            >
              casts
              <div class="d-grid grid-cols-1 grid-cols-md-2 gap-4">
                <v-card
                  v-for="character in anilistData.characters.edges"
                  :key="character.node.id"
                  :rounded="smAndUp"
                >
                  <div
                    class="d-grid grid-cols-2"
                  >
                    <div class="d-grid grid-cols-4">
                      <v-img
                        :src="character.node.image.large"
                        :lazy-src="character.node.image.medium"
                      />

                      <div class="px-3 py-1 col-span-3 d-flex flex-column justify-space-between">
                        <div>
                          {{ character.node.name.full }}
                        </div>
                        <div class="text-capitalize">
                          {{ character.role }}
                        </div>
                      </div>
                    </div>
                    <div class="d-grid grid-cols-4">
                      <div class="px-3 py-1 col-span-3 text-right d-flex flex-column justify-space-between">
                        <div>
                          {{ character.voiceActors[0].name.full }}
                        </div>
                        <div>
                          Japanese
                        </div>
                      </div>
                      <v-img
                        :src="character.voiceActors[0].image.large"
                        :lazy-src="character.voiceActors[0].image.medium"
                      />
                    </div>
                  </div>
                </v-card>
              </div>
            </section>
          </v-expand-transition>
        </v-col>
        <v-col
          cols="12"
          md="4"
        >
          <section class="mb-6">
            <v-card :rounded="smAndUp">
              <v-card-title class="d-flex">
                Publication
              </v-card-title>
            </v-card>
          </section>
        </v-col>
      </v-row>
    </v-container>
  </Layout>
</template>
