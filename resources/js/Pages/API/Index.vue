<template>
  <web-layout>
    <template #header>
      API Tokens
    </template>

    <div>
      <div class="mx-auto py-10 px-sm-6 px-lg-8">
        <api-token-manager
          :tokens="tokens"
          :available-permissions="availablePermissions"
          :default-permissions="defaultPermissions"
        />
      </div>
    </div>
  </web-layout>
</template>

<script>
/* eslint-disable vue/require-prop-types */
import ApiTokenManager from './ApiTokenManager'
import WebLayout from '@/Layouts/Web/Index.vue'

export default {
  components: {
    ApiTokenManager,
    WebLayout
  },
  props: ['tokens', 'availablePermissions', 'defaultPermissions']
}
</script>
