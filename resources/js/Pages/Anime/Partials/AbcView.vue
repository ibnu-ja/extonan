<script lang="ts" setup>
import { PaginatedResponse } from '@/types'
import { AnimeData } from '@/types/anime'
import { groupBy, map, sortBy } from 'lodash-es'
import { computed } from 'vue'
import { Link as InertiaLink } from '@inertiajs/vue3'
import { useLanguages } from '@/composables/useLanguages'

const props = defineProps<{
  canCreate: boolean
  canViewUnpublished: boolean
  anime: PaginatedResponse<AnimeData>
}>()

const { translate } = useLanguages()

const animeGrouped = computed(() => {
  let map1 = map(
    groupBy(props.anime.data, (o) => {
      const title = translate(o.title)
      const firstLetter = Array.from(title.toString())[0].toUpperCase()
      return /^[A-Z]$/.test(firstLetter!) ? firstLetter : '#'
    }),
    (contacts, letter) => ({ letter, contacts }),
  )
  return sortBy(map1, 'letter')
})
</script>

<template>
  <v-container>
    <template
      v-for="letters in animeGrouped"
      :key="letters.letter"
    >
      <h2 class="text-h4">
        <a
          :href="`#${letters.letter}`"
          class="text-primary no-underline hover:underline cursor-pointer"
        >
          {{ letters.letter }}
        </a>
      </h2>
      <v-divider class="my-2" />
      <ul
        class="ps-5 mb-6"
      >
        <li
          v-for="singleAnime in letters.contacts"
          :key="singleAnime.id"
          class="py-2"
        >
          <InertiaLink
            :href="singleAnime.link"
            class="no-underline hover:underline"
          >
            {{ translate(singleAnime.title) }}
          </InertiaLink>
          <v-chip
            v-if="!singleAnime.is_published"
            class="ml-2"
            color="success"
          >
            Draft
          </v-chip>
        </li>
      </ul>
      <!--<div-->
      <!--  v-for="singleAnime in letters.contacts"-->
      <!--  :key="singleAnime.id"-->
      <!--  class="mb-4"-->
      <!--&gt;-->
      <!--  <InertiaLink-->
      <!--    :link="false"-->
      <!--    :as="VListItem"-->
      <!--    :href="singleAnime.link"-->
      <!--    class="hover:underline cursor-pointer px-0"-->
      <!--  >-->
      <!--    <v-icon :icon="mdiCircleSmall" />-->
      <!--    {{ translate(singleAnime.title) }}-->
      <!--  </InertiaLink>-->
      <!--</div>-->
    </template>
  </v-container>
</template>
