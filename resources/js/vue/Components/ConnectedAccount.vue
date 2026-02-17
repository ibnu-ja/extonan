<script setup lang="ts">
import { SocialstreamProviders } from '@/types'
import { useProvider } from './SocialstreamIcons/Provider'

const { getIcon } = useProvider()
withDefaults(defineProps<{
  provider: SocialstreamProviders
  createdAt: string
}>(), {
  createdAt: '',
})

</script>

<template>
  <v-list>
    <v-list-item
      :title="provider.name"
      :subtitle="createdAt || 'Not connected.'"
    >
      <template #prepend>
        <v-icon
          size="40"
          :icon="getIcon(provider.id)"
        />
      </template>
      <template #append>
        <slot name="action" />
      </template>
    </v-list-item>
  </v-list>
</template>
