<script lang="ts" setup>
import { AnimeMediaAutofillResponse } from '@/types/anilist'
import dayjs from 'dayjs'
import objectSupport from 'dayjs/plugin/objectSupport'

dayjs.extend(objectSupport)

const props = defineProps<{
  data: AnimeMediaAutofillResponse
}>()

const airingDate = dayjs({
  year: props.data.startDate.year ?? undefined,
  month: props.data.startDate.month ? props.data.startDate.month - 1 : undefined,
  day: props.data.startDate.day ? props.data.startDate.day - 1 : undefined,
}).format('D MMM YYYY')

</script>

<template>
  <div>
    <v-divider />
    <v-card-text>
      <!-- metadata -->
      <div v-if="data.startDate">
        <v-list-subheader>
          Airing Date
        </v-list-subheader>
        <!--dayjs is 0 indexed-->
        {{ airingDate }}
      </div>
      <div v-if="data.episodes">
        <v-list-subheader>
          Episodes
        </v-list-subheader>
        {{ data.episodes }}
      </div>
      <div v-if="data.studios">
        <v-list-subheader>
          Studio
        </v-list-subheader>
        <!--dayjs is 0 indexed-->

        <div class="d-flex flex-wrap gap-1">
          <v-chip
            v-for="{node: studio} in data.studios.edges"
            :key="studio.id"
          >
            {{ studio.name }}
          </v-chip>
        </div>
      </div>
      <div v-if="data.season && data.seasonYear">
        <v-list-subheader>
          Season
        </v-list-subheader>
        <v-chip>
          {{ data.season }} {{ data.seasonYear }}
        </v-chip>
      </div>

      <div
        v-if="data.genres && data.genres.length> 0"
      >
        <v-list-subheader>
          Genres
        </v-list-subheader>
        <div class="d-flex flex-wrap gap-1">
          <v-chip
            v-for="genre in data.genres"
            :key="genre"
          >
            {{ genre }}
          </v-chip>
        </div>
      </div>
    </v-card-text>
  </div>
</template>
