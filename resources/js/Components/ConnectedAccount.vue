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
      <!--      <v-list-item-title>-->
      <!--        {{ provider.name }}-->
      <!--      </v-list-item-title>-->
      <!--      <v-list-item-subtitle v-if="createdAt">-->
      <!--        Conn-->
      <!--      </v-list-item-subtitle>-->
    </v-list-item>
  </v-list>
<!--  <div>-->
<!--    <div class="px-3 flex items-center justify-between">-->
<!--      <div class="flex items-center">-->
<!--        <v-icon :icon="getIcon(provider.id)" />-->

<!--        <div class="ml-2">-->
<!--          <div class="text-sm font-semibold text-gray-600">-->
<!--            {{ provider.name }}-->
<!--          </div>-->

<!--          <div-->
<!--            v-if="createdAt !== null"-->
<!--            class="text-xs text-gray-500"-->
<!--          >-->
<!--            Connected {{ createdAt }}-->
<!--          </div>-->

<!--          <div-->
<!--            v-else-->
<!--            class="text-xs text-gray-500"-->
<!--          >-->
<!--            Not connected.-->
<!--          </div>-->
<!--        </div>-->
<!--      </div>-->

<!--      <slot name="action" />-->
<!--    </div>-->
<!--  </div>-->
</template>
