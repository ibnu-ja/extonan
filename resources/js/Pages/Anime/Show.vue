<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { AnimeData } from '@/types/anime'

defineProps<{
  anime: AnimeData
}>()
</script>

<template>
  <AppLayout>
    <Head :title="anime.title.en!" />
  </AppLayout>
  <!--  -->
</template>
