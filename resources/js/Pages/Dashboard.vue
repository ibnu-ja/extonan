<script setup lang="ts">
import Welcome from '@/Components/Welcome.vue'
import Layout from '@/Layouts/Layout.vue'
import { BreadcrumbItem } from '@/types/index.js'

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
  <Layout
    :breadcrumbs="breadcrumbs"
    title="Dashboard"
  >
    <template #header>
      <h1 class="text-h4 text-md-h3 mb-4">
        Dashboard
      </h1>
    </template>

    <Welcome />
  </Layout>
</template>
