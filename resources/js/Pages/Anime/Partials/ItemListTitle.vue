<script lang="ts" setup>
import { mdiDotsVertical, mdiPencil, mdiSend } from '@mdi/js'
import { mdiDelete } from '@mdi/js/commonjs/mdi'
import { Permissions } from '@/types'
import InertiaLink from '@/Components/InertiaLink.vue'
import { VListItem } from 'vuetify/components'
import { computed, inject } from 'vue'
import { route as ziggyRoute } from 'ziggy-js'

const props = defineProps<{
  permissions?: Permissions
  overhead?: string | null
  subtitle?: string | null
  title: string
  postableId?: number
  postId?: number
  isPublished: boolean
}>()

const route = inject('route') as typeof ziggyRoute

const edit = computed(() => {
  if (!props.permissions?.update || !props.postableId || !props.postId)
    return undefined
  return route('post.edit', [props.postableId, props.postId])
})

// const publish = computed(() => {
//   if (!props.permissions?.publish || props.postableId || props.postId)
//     return undefined
//   return route('post.publish', [props.postableId, props.postId])
// })
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
            v-if="!isPublished"
            :prepend-icon="mdiSend"
            title="Publish"
            :disabled="!permissions?.publish"
          >
            <template #prepend>
              <v-icon
                :icon="mdiSend"
                color="primary"
              />
            </template>
          </v-list-item>
          <InertiaLink
            :as="VListItem"
            :disabled="!permissions?.update"
            :href="edit"
            title="Edit"
          >
            <template #prepend>
              <v-icon
                :icon="mdiPencil"
                color="secondary"
              />
            </template>
          </InertiaLink>
          <v-list-item
            :disabled="!permissions?.delete"
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
