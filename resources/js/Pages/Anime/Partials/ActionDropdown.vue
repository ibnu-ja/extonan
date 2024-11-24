<script lang="ts" setup>
import { mdiDelete, mdiDotsVertical, mdiPencil, mdiSend } from 'mdi-js-es'
import { Permissions } from '@/types'
import { VListItem } from 'vuetify/components'
import { openConfirmationDialog } from '@/composables/useDialog'
import { router } from '@inertiajs/vue3'
import InertiaLink from '@/Components/InertiaLink'

const props = withDefaults(defineProps<{
  permissions?: Permissions
  isPublished: boolean
  deleteUrl?: string
  editUrl?: string
  variant?: 'flat' | 'text' | 'elevated' | 'tonal' | 'outlined' | 'plain'
  color?: string
}>(), {
  variant: 'plain',
  permissions: undefined,
  deleteUrl: undefined,
  editUrl: undefined,
  color: undefined,
})

const deletePost = async () => {
  try {
    const confirmed = await openConfirmationDialog('Are you sure want to delete this item?')
    if (confirmed && props.deleteUrl) {
      router.delete(props.deleteUrl)
    }
  } catch {
    //
  }
}
</script>
<template>
  <v-menu>
    <template #activator="{ props: propss }">
      <v-btn
        density="comfortable"
        :variant
        :color
        :icon="mdiDotsVertical"
        v-bind="propss"
        @click.prevent
      />
    </template>
    <v-list density="comfortable">
      <!--TODO publish action-->
      <v-list-item
        v-if="!isPublished"
        :prepend-icon="mdiSend"
        title="Publish (TODO)"
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
        :href="editUrl"
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
        @click.prevent="deletePost"
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
