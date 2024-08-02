<script setup lang="ts">
import Welcome from '@/Components/Welcome.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Home',
    disabled: false,
    href: '/',
  },
  {
    title: 'Dashboard',
    disabled: true,
  },
]
</script>

<template>
  <AppLayout
    :breadcrumbs="breadcrumbs"
    title="Dashboard"
  >
    <template #header>
      <h1 class="text-h4 text-md-h3">
        Dashboard
      </h1>
    </template>

    <Welcome />
  </AppLayout>
</template>
