<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { AnimeData } from '@/types/anime'

defineProps<{
  anime: AnimeData
  post: unknown
}>()
</script>

<template>
  <Head title="Bwang" />

  <v-container>
    {{ anime }}
  </v-container>

  <v-divider />

  <v-container>
    {{ post }}
  </v-container>
</template>
