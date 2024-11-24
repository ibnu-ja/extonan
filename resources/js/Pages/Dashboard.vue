<script setup lang="ts">
import Welcome from '@/Components/Welcome.vue'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({
  layout: AppLayout,
})
</script>

<template>
  <PageHeader title="Dashboard" />
  <Welcome />
</template>
