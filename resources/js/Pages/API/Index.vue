<template>
  <dash-layout>
    <template #header>
      <h3>
        API Tokens
      </h3>
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
  </dash-layout>
</template>

<script>
import ApiTokenManager from './ApiTokenManager'
import DashLayout from '@/Layouts/Dash/Index.vue'

export default {
  components: {
    ApiTokenManager,
    DashLayout
  },
  // eslint-disable-next-line vue/require-prop-types
  props: ['tokens', 'availablePermissions', 'defaultPermissions']
}
</script>
