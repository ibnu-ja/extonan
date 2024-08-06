<script lang="ts">

import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  layout: AppLayout,
}
</script>

<script setup lang="ts">
import Welcome from '@/Components/Welcome.vue'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
</script>

<template>
  <PageHeader title="Dashboard" />
  <Welcome />
</template>
