<script setup lang="ts">

import Layout from '@/Layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { useDisplay } from 'vuetify'
import { mdiClose, mdiContentSave, mdiSend } from '@mdi/js'
import dayjs from 'dayjs'

import objectSupport from 'dayjs/plugin/objectSupport'
import { mdiDelete } from '@mdi/js/commonjs/mdi'

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

dayjs.extend(objectSupport)
type AnimeForm = {
  title: TranslatableField
  description: TranslatableField
  anilist_id: number | null
  is_published: boolean
}

const form = useForm<AnimeForm>({
  title: {
    en: null,
    id: null,
    native: null,
    romaji: null,
  },
  is_published: false,
  description: {
    en: null,
    id: null,
  },
  anilist_id: null,
},
)
const apType = ref<'MAL' | 'Anilist'>('MAL')
const apiSearchId = ref<number | null>(null)
const anilistData = ref()
const show = ref(false)

const { smAndUp, mdAndUp } = useDisplay()
const currentLang = ref<LanguageItem>(languages[0])
//

const anilistHitApi = async () => {
  if (apiSearchId.value == null || apiSearchId.value < 0) {
    return
  }
  const query = `
    query ($id: Int, $idMal: Int) {
      Media (id: $id, idMal: $idMal , type: ANIME) {
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
        studios {
          edges {
            node {
              id
              name
            }
          }
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

  let variables
  if (apType.value == 'MAL') {
    variables = {
      idMal: apiSearchId.value,
    }
  } else variables = { id: apiSearchId.value }

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
    form.anilist_id = anime.id
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
      <div class="d-flex justify-space-between align-end">
        <h1 class="text-h4 text-md-h3">
          Create Anime
        </h1>
        <div class="d-flex gap-2">
          <v-btn
            variant="outlined"
            color="error"
            :icon="!mdAndUp ? mdiDelete : undefined"
            :prepend-icon="mdAndUp ? mdiDelete : undefined"
            :text="mdAndUp ? 'Delete' : undefined"
          />
          <v-btn
            variant="outlined"
            color="secondary"
            :icon="!mdAndUp ? mdiContentSave : undefined"
            :prepend-icon="mdAndUp ? mdiContentSave : undefined"
            :text="mdAndUp ? 'Save' : undefined"
          />
          <v-btn
            color="primary"
            :icon="!mdAndUp ? mdiSend : undefined"
            :prepend-icon="mdAndUp ? mdiSend : undefined"
            :text="mdAndUp ? 'Publish' : undefined"
          />
        </div>
      </div>
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
                  :label="`Description (${currentLang.value})`"
                  variant="outlined"
                  hide-details="auto"
                  class="mb-4"
                />
              </v-card-text>
            </v-card>
          </section>

          <v-expand-transition>
            <section
              v-if="anilistData && anilistData.characters?.edges"
              class="mb-6"
            >
              casts
              <div class="d-grid grid-cos-1 grid-cols-md-2 gap-4 text-body-2">
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
                        <div class="text-caption font-weight-light">
                          {{ character.role }}
                        </div>
                      </div>
                    </div>
                    <v-slide-x-transition>
                      <div
                        v-if="character.voiceActors[0]?.name"
                        class="d-grid grid-cols-4"
                      >
                        <div class="px-3 py-1 col-span-3 text-right d-flex flex-column justify-space-between">
                          <div>
                            {{ character.voiceActors[0].name.full }}
                          </div>
                          <div class="font-weight-light">
                            Japanese
                          </div>
                        </div>
                        <v-img
                          :src="character.voiceActors[0].image.large"
                          :lazy-src="character.voiceActors[0].image.medium"
                        />
                      </div>
                    </v-slide-x-transition>
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
          <div>
            <v-expand-x-transition>
              <v-card
                v-if="anilistData && anilistData.coverImage"
                class="mb-4"
              >
                <v-img
                  :src="anilistData.coverImage.extraLarge"
                  :lazy-src="anilistData.coverImage.medium"
                />
              </v-card>
            </v-expand-x-transition>
            <v-card :rounded="smAndUp">
              <v-card-item>
                <v-card-title class="d-flex">
                  Metadata
                </v-card-title>
              </v-card-item>
              <v-divider />
              <v-card-text>
                <!-- template -->
                <v-select
                  v-model="apType"
                  label="Api Type"
                  variant="outlined"
                  hide-details
                  class="mb-4"
                  :items="['MAL', 'Anilist']"
                />
                <v-text-field
                  v-model="apiSearchId"
                  :label="apType + ' ID'"
                  type="number"
                  placeholder="placeholder"
                  variant="outlined"
                  hide-details="auto"
                />
              </v-card-text>
              <v-divider />
              <v-card-actions>
                <v-expand-x-transition>
                  <v-btn
                    :append-icon="mdiClose"
                    text="Clear"
                    @click="clearAnilist"
                  />
                </v-expand-x-transition>
                <v-spacer />
                <v-btn
                  text="Autofill"
                  @click="anilistHitApi"
                />
              </v-card-actions>

              <v-expand-transition>
                <div v-if="anilistData">
                  <v-divider />
                  <v-card-text>
                    <!-- metadata -->
                    <div v-if="anilistData.startDate">
                      <v-list-subheader>
                        Airing Date
                      </v-list-subheader>
                      <!--dayjs is 0 indexed-->
                      {{ dayjs({
                        year: anilistData.startDate.year,
                        month: anilistData.startDate.month-1,
                        day: anilistData.startDate.day -1
                      }).format('D MMM YYYY') }}
                    </div>
                    <div v-if="anilistData.episodes">
                      <v-list-subheader>
                        Episodes
                      </v-list-subheader>
                      {{ anilistData.episodes }}
                    </div>
                    <div v-if="anilistData.studios">
                      <v-list-subheader>
                        Studio
                      </v-list-subheader>
                      <!--dayjs is 0 indexed-->

                      <div class="d-flex flex-wrap gap-1">
                        <v-chip
                          v-for="{node: studio} in anilistData.studios.edges"
                          :key="studio.id"
                        >
                          {{ studio.name }}
                        </v-chip>
                      </div>
                    </div>
                    <div v-if="anilistData.season && anilistData.seasonYear">
                      <v-list-subheader>
                        Season
                      </v-list-subheader>
                      <v-chip>
                        {{ anilistData.season }} {{ anilistData.seasonYear }}
                      </v-chip>
                    </div>

                    <div
                      v-if="anilistData.genres && anilistData.genres.length> 0"
                    >
                      <v-list-subheader>
                        Genres
                      </v-list-subheader>
                      <div class="d-flex flex-wrap gap-1">
                        <v-chip
                          v-for="genre in anilistData.genres"
                          :key="genre"
                        >
                          {{ genre }}
                        </v-chip>
                      </div>
                    </div>
                  </v-card-text>
                </div>
              </v-expand-transition>
            </v-card>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </Layout>
</template>
