<script lang="ts" setup>
import { PaginatedResponse } from '@/types'
import { AnimeData } from '@/types/anime'
import { groupBy, map, sortBy } from 'lodash-es'
import { computed } from 'vue'
import InertiaLink from '@/Components/InertiaLink'
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
        {{ letters.letter }}
      </h2>
      <ul
        v-for="singleAnime in letters.contacts"
        :key="singleAnime.id"
      >
        <li>
          <InertiaLink
            :href="singleAnime.link"
            class="text-decoration-none"
          >
            {{ translate(singleAnime.title) }}
          </InertiaLink>
        </li>
      </ul>
    </template>
  </v-container>
</template>
