<script lang="ts" setup>
import { Permissions } from '@/types'
import { VListItem } from 'vuetify/components'
import ActionDropdown from '@/Pages/Anime/Partials/ActionDropdown.vue'

defineProps<{
  permissions?: Permissions
  overhead?: string | null
  subtitle?: string | null
  title: string
  isPublished: boolean
  deleteUrl?: string
  editUrl?: string
}>()

</script>

<template>
  <v-list-item class="p-0">
    <div class="mx-2">
      <div
        v-if="overhead"
        class="text-subtitle-2 text-medium-emphasis"
      >
        {{ overhead }}
      </div>
      <div class="text-subtitle-1 list-title">
        {{ title }}
      </div>
      <div
        v-if="subtitle"
        class="text-subtitle-2 text-medium-emphasis"
      >
        {{ subtitle }}
      </div>
    </div>
    <template #append>
      <ActionDropdown
        :edit-url
        :delete-url
        :permissions
        :is-published
      />
    </template>
  </v-list-item>
</template>

<style scoped>
.list-title {
  display: block;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
}
</style>
