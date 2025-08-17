<script lang="ts" setup>
import { mdiChevronLeft, mdiChevronRight } from 'mdi-js-es'
import { VBtn } from 'vuetify/components'
import { Link as InertiaLink } from '@inertiajs/vue3'
import { Link } from '@/types'

withDefaults(defineProps<{
  links: Link[]
}>(), {
  links: () => [],
})

</script>

<template>
  <div
    class="pagination"
    role="navigation"
  >
    <ul class="pagination__list">
      <li
        v-for="(link, i) in links"
        :key="i"
        class="pagination__item"
      >
        <InertiaLink
          v-if="link.url"
          :as="VBtn"
          :active="link.active"
          :disabled="link.label === '...' || !link.url || link.active"
          size="small"
          icon
          :href="link.url"
          :variant="link?.active ? undefined : 'text'"
          preserve-scroll
        >
          <v-icon
            v-if="i === 0"
            :icon="mdiChevronLeft"
          />
          <v-icon
            v-else-if="i === links.length-1"
            :icon="mdiChevronRight"
          />
          <template v-else>
            {{ link?.label }}
          </template>
        </InertiaLink>
        <VBtn
          v-else
          :active="link.active"
          :disabled="link.label === '...' || !link.url || link.active"
          size="small"
          :variant="link?.active ? undefined : 'text'"
          icon
        >
          <v-icon
            v-if="i === 0"
            :icon="mdiChevronLeft"
          />
          <v-icon
            v-else-if="i === links.length-1"
            :icon="mdiChevronRight"
          />
        </VBtn>
      </li>
    </ul>
  </div>
</template>

<style lang="scss">
@use "../../css/settings";

.pagination {
  &__list {
    display: inline-flex;
    list-style-type: none;
    justify-content: center;
    width: 100%;
  }

  &__item {
    margin: settings.$pagination-item-margin;
  }

  .v-btn {
    border-radius: 4px;
  }
}

</style>
