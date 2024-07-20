<script setup lang="ts">
import { SocialstreamProviders } from '@/types'
import { useProvider } from './SocialstreamIcons/Provider'

const { getIcon } = useProvider()

withDefaults(
  defineProps<{
    prompt: string
    error: string
    providers: SocialstreamProviders[]
  }>(), {
    prompt: 'Or Login Via',
  },
)

</script>

<template>
  <div class="d-flex justify-center align-center my-4">
    <v-divider />
    <div
      class="mx-2"
      style="min-width: fit-content;"
    >
      {{ prompt }}
    </div>
    <v-divider />
  </div>

  <div class="d-flex flex-column flex-warp justify-center gap-4">
    <v-btn
      v-for="provider in providers"
      :key="provider.id"
      block
      variant="outlined"
      :text="provider.buttonLabel"
      :prepend-icon="getIcon(provider.id)"
      :href="route('oauth.redirect', provider.id)"
    />
  </div>
</template>
