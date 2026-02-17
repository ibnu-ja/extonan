<script lang="ts" setup>
import { AnimeData } from '@/types/anime'
import { groupBy, map, sortBy } from 'lodash-es'
import { computed, inject } from 'vue'
import { Head, Link as InertiaLink } from '@inertiajs/vue3'
import { useLanguages } from '@/composables/useLanguages'
import AppLayout from '@/Layouts/AppLayout.vue'
import { VBtn } from 'vuetify/components'
import { mdiAlphabeticalVariant, mdiFormatListNumbered, mdiPlus } from 'mdi-js-es'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
import { route as ziggyRoute } from 'ziggy-js'
import { useDisplay } from 'vuetify'

const props = defineProps<{
  canCreate: boolean
  canViewUnpublished: boolean
  anime: AnimeData[]
}>()

const { translate } = useLanguages()

const animeGrouped = computed(() => {
  let map1 = map(
    groupBy(props.anime, (o) => {
      const title = translate(o.title)
      const firstLetter = Array.from(title.toString())[0].toUpperCase()
      return /^[A-Z]$/.test(firstLetter!) ? firstLetter : '#'
    }),
    (contacts, letter) => ({ letter, contacts }),
  )
  return sortBy(map1, 'letter')
})

defineOptions({
  name: 'AnimeAZ',
  layout: AppLayout,
})

const route = inject('route') as typeof ziggyRoute

const { mdAndUp } = useDisplay()
</script>

<template>
  <Head title="Anime AZ Index" />

  <!-- Page Heading -->
  <!-- mt-md-6-->
  <PageHeader title="Anime List">
    <template #append>
      <InertiaLink
        v-if="canCreate"
        :as="VBtn"
        :href="route('anime.create')"
        color="primary"
        :icon="!mdAndUp ? mdiPlus : undefined"
        :prepend-icon="mdAndUp ? mdiPlus : undefined"
        :text="mdAndUp ? 'Create' : undefined"
      />
    </template>
  </PageHeader>

  <v-container
    :max-width="1800"
  >
    <div class="flex">
      <v-btn-toggle
        class="ml-auto"
        :mandatory="true"
      >
        <InertiaLink
          active
          :as="VBtn"
          :href="route('anime.az', {...route().params})"
          value="abc"
          :icon="mdiAlphabeticalVariant"
        />
        <InertiaLink
          :as="VBtn"
          :href="route('anime.index')"
          :icon="mdiFormatListNumbered"
        />
      </v-btn-toggle>
    </div>
    <div>
      <div
        class="pagination"
        role="navigation"
      >
        <ul class="pagination__list">
          <li
            v-for="link in animeGrouped"
            :key="link.letter"
            class="pagination__item"
          >
            <VBtn
              size="small"
              icon
              :href="`#${link.letter}`"
              variant="text"
            >
              {{ link.letter }}
            </VBtn>
          </li>
        </ul>
      </div>
    </div>
    <template
      v-for="letters in animeGrouped"
      :key="letters.letter"
    >
      <h2
        :id="letters.letter"
        class="text-h4"
      >
        <a
          :href="`#${letters.letter}`"
          class="text-primary no-underline hover:underline cursor-pointer"
        >
          {{ letters.letter }}
        </a>
      </h2>
      <v-divider class="my-2" />
      <ul
        class="ps-5 mb-6 list-disc"
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

<style lang="scss">
@use "../../../css/settings";

.pagination {
  &__list {
    display: inline-flex;
    list-style-type: none;
    justify-content: center;
    width: 100%;
  }

  &__item {
    margin: settings.$pagination-item-margin;
  }

  .v-btn {
    border-radius: 4px;
  }
}

</style>
