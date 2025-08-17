<script setup lang="ts">

import { VCard, VChip } from 'vuetify/components'
import { Link } from '@inertiajs/vue3'
import { AnimeMediaAutofillResponse } from '@/types/anilist'
import { UserRelation } from '@/types'
import dayjs from 'dayjs'

type Props = {
  metadata: AnimeMediaAutofillResponse
  airingDate: string
  // eslint-disable-next-line vue/prop-name-casing
  created_at: Date | string | null
  // eslint-disable-next-line vue/prop-name-casing
  updated_at: Date | string | null
  // eslint-disable-next-line vue/prop-name-casing
  published_at: Date | string | null
  // eslint-disable-next-line vue/prop-name-casing
  is_published: boolean
  author: UserRelation
  publisher: UserRelation
  alternativeTitles?: string[]
}

defineProps<Props>()

</script>

<template>
  <v-theme-provider theme="dark">
    <v-card
      variant="tonal"
      rounded="xl"
      class="bg-surface mb-4"
    >
      <v-card-text>
        <div v-if="alternativeTitles">
          <v-list-subheader>
            Alternative Titles
          </v-list-subheader>
          <ul class="ml-4 list-disc">
            <li
              v-for="title in alternativeTitles"
              :key="title"
            >
              {{ title }}
            </li>
          </ul>
        </div>
        <!-- metadata -->
        <div v-if="airingDate">
          <v-list-subheader>
            Airing Date
          </v-list-subheader>
          {{ airingDate }}
          <!--dayjs is 0 indexed-->
        </div>
        <div v-if="metadata.episodes">
          <v-list-subheader>
            Episodes
          </v-list-subheader>
          {{ metadata.episodes }}
        </div>
        <div v-if="metadata.studios">
          <v-list-subheader>
            Studio
          </v-list-subheader>
          <!--dayjs is 0 indexed-->

          <div class="flex flex-wrap gap-1">
            <v-chip
              v-for="{node: studio} in metadata.studios.edges"
              :key="studio.id"
            >
              {{ studio.name }}
            </v-chip>
          </div>
        </div>
        <div v-if="metadata?.tags">
          <v-list-subheader>
            Tags
          </v-list-subheader>
          <div class="flex flex-wrap gap-1">
            <Link
              v-for="tag in metadata.tags"
              :key="tag.id"
              :as="VChip"
              :href="route('anime.index', { filter: {tag_in: tag.name} })"
            >
              {{ tag.name }}
            </Link>
          </div>
        </div>
        <div v-if="metadata.season && metadata.seasonYear">
          <v-list-subheader>
            Season
          </v-list-subheader>
          <Link
            :as="VChip"
            :href="route('anime.index', { filter: {season_in: `${metadata.season} ${metadata.seasonYear}`} })"
          >
            {{ metadata.season }} {{ metadata.seasonYear }}
          </Link>
        </div>
      </v-card-text>
      <v-divider />
      <v-card-item title="Post Info" />
      <v-card-text>
        <div>
          <v-list-subheader>Created at</v-list-subheader>
          <span :title="dayjs(created_at).toString()">
            {{ dayjs(created_at).calendar() }}</span>
        </div>
        <div>
          <v-list-subheader>
            Last modified
          </v-list-subheader>
          <span :title="dayjs(updated_at).toString()">
            {{ dayjs(updated_at).calendar() }}</span>
        </div>
        <div>
          <v-list-subheader>
            Author
          </v-list-subheader>
          {{ author?.name }}
        </div>
        <div>
          <v-list-subheader>
            Published at
          </v-list-subheader>
          <v-chip
            v-if="!is_published"
            color="success"
            variant="flat"
          >
            Draft
          </v-chip>
          <span
            v-else
            :title="dayjs(published_at).toString()"
          >
            {{ dayjs(published_at).calendar() }}
          </span>
        </div>
        <div
          v-if="is_published"
        >
          <v-list-subheader>
            Published by
          </v-list-subheader>
          {{ publisher!.name }}
        </div>
      </v-card-text>
    </v-card>
  </v-theme-provider>
</template>
