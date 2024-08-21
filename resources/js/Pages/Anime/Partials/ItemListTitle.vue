<script lang="ts" setup>
import { mdiDotsVertical, mdiPencil, mdiSend } from '@mdi/js'
import { mdiDelete } from '@mdi/js/commonjs/mdi'
import { Permissions } from '@/types'

defineProps<{
  permissions?: Permissions
  overhead?: string | null
  subtitle?: string | null
  title: string
}>()
</script>

<template>
  <v-list-item class="pa-0">
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
    <template
      v-if="permissions?.publish || permissions?.update || permissions?.delete"
      #append
    >
      <v-menu>
        <template #activator="{ props: propss }">
          <v-btn
            density="comfortable"
            variant="plain"
            :icon="mdiDotsVertical"
            v-bind="propss"
            @click.prevent
          />
        </template>
        <v-list density="comfortable">
          <v-list-item
            :prepend-icon="mdiSend"
            title="Publish"
          >
            <template #prepend>
              <v-icon
                :icon="mdiSend"
                color="primary"
              />
            </template>
          </v-list-item>
          <v-list-item
            title="Edit"
          >
            <template #prepend>
              <v-icon
                :icon="mdiPencil"
                color="secondary"
              />
            </template>
          </v-list-item>
          <v-list-item
            title="Delete"
          >
            <template #prepend>
              <v-icon
                :icon="mdiDelete"
                color="error"
              />
            </template>
          </v-list-item>
        </v-list>
      </v-menu>
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
