<script setup lang="ts">
import ApiTokenManager from '@/Pages/API/Partials/ApiTokenManager.vue'
import Layout from '@/Layouts/AppLayout.vue'
import { ApiToken } from '@/types'

defineProps<{
  tokens: ApiToken[]
  availablePermissions: string[]
  defaultPermissions: string[]
}>()
</script>

<template>
  <Layout title="API Tokens">
    <template #header>
      <h1 class="text-h4 text-md-h3">
        Profile
      </h1>
    </template>

    <v-container>
      <ApiTokenManager
        :tokens="tokens"
        :available-permissions="availablePermissions"
        :default-permissions="defaultPermissions"
      />
    </v-container>
  </Layout>
</template>
