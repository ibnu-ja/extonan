<script setup lang="ts">
import HorizontalEpisodeCard from '@/Pages/Anime/Partials/HorizontalEpisodeCard.vue'
import { MV } from '@/types/mv'
import { useLanguages } from '@/composables/useLanguages'
import dayjs from 'dayjs'

defineProps<{
  post: MV
}>()

const { translate } = useLanguages()

</script>

<template>
  <HorizontalEpisodeCard
    :show-action="!!$page.props.auth.user"
    :image="post.thumbnail?.extraLarge"
    :lazy-img="post.thumbnail?.medium"
    :href="route('mv.show', post)"
    :permissions="post.can"
    :delete-url="route('mv.destroy', post)"
    :edit-url="route('mv.edit', post)"
    :is-published="post.is_published"
  >
    <template #content>
      <div class="text-subtitle-1 list-title">
        {{ translate(post.title) }}
      </div>
      <div
        class="text-subtitle-2 text-medium-emphasis"
      >
        <template
          v-if="!post.is_published"
        >
          <span
            class="text-success"
          >
            Draft
          </span> by
        </template>
        <template v-else>
          {{ dayjs(post.published_at).format('D MMM YYYY') }} &bull;
        </template>
        {{ post.author.name }}
      </div>
    </template>
  </HorizontalEpisodeCard>
</template>
