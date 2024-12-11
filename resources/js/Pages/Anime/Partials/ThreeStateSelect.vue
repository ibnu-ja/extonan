<script lang="ts" setup>

import { useAnime } from '@/composables/useAniList'
import { computed, ref } from 'vue'
import { VTextField } from 'vuetify/components'
import ThreeStateCheckbox from '@/Pages/Anime/Partials/ThreeStateCheckbox.vue'

withDefaults(
  defineProps<{
    maxHeight?: number | string
    variant?: 'filled' | 'underlined' | 'outlined' | 'plain' | 'solo' | 'solo-inverted' | 'solo-filled'
  }>(), {
    maxHeight: 400,
    variant: 'filled',
  },
)

const { tags } = useAnime()
const tagsIn = defineModel<string[]>({ default: () => [] })
// const tagsIn = ref<string[]>([])
const tagsNotIn = defineModel<string[]>('tagsNotIn', { default: () => [] })
const searchTextField = ref<InstanceType<typeof VTextField>>()
const searchKeyword = ref<string>('')
const searchedTags = computed(() => {
  let displayTags
  if (searchKeyword.value?.length > 0) {
    displayTags = tags.filter(
      tag => tag.toLocaleLowerCase().indexOf(searchKeyword.value.toLowerCase()) > -1,
    )
  } else displayTags = tags

  return displayTags.map((season) => {
    let state
    let color
    // let checkboxButton
    if (tagsIn.value.includes(season)) {
      color = 'success'
      state = 'in'
    } else if (tagsNotIn.value.includes(season)) {
      color = 'error'
      state = 'notIn'
    }

    return {
      active: color != undefined,
      activeColor: color,
      name: season,
      state: state,
    }
  })
})

const clickMenuFilter = (tag: string) => {
  if (!tagsIn.value.includes(tag) && !tagsNotIn.value.includes(tag)) {
    tagsIn.value.push(tag)
  } else if (tagsIn.value.includes(tag) && !tagsNotIn.value.includes(tag)) {
    removeTag(tag)
    tagsNotIn.value.push(tag)
  } else if (!tagsIn.value.includes(tag) && tagsNotIn.value.includes(tag)) {
    removeTagNot(tag)
  }
}

const removeTag = (tag: string) => {
  tagsIn.value = tagsIn.value.filter(seasonItem => seasonItem != tag)
}
const removeTagNot = (tag: string) => {
  tagsNotIn.value = tagsNotIn.value.filter(seasonItem => seasonItem != tag)
}

</script>

<template>
  <v-menu
    :close-on-content-click="false"
    :max-height="400"
  >
    <template #activator="{ props }">
      <v-text-field
        ref="searchTextField"
        v-model="searchKeyword"
        :variant="props.variant"
        hide-details
        v-bind="props"
      >
        <div class="flex flex-wrap gap-2">
          <v-chip
            v-for="tag in tagsIn"
            :key="tag"
            closable
            color="success"
            @close.prevent="removeTag"
            @click:close.prevent="removeTag(tag)"
          >
            {{ tag }}
          </v-chip>

          <v-chip
            v-for="tag in tagsNotIn"
            :key="tag"
            closable
            color="error"
            @close.prevent="removeTagNot"
            @click:close.prevent="removeTagNot(tag)"
          >
            {{ tag }}
          </v-chip>
        </div>
      </v-text-field>
    </template>
    <v-list>
      <v-list-item
        v-for="tag in searchedTags"
        :key="tag.name"
        :value="tag.name"
        :active="tag.active"
        :color="tag.activeColor"
        @click="clickMenuFilter(tag.name)"
      >
        <template #prepend>
          <ThreeStateCheckbox :state="tag.state" />
        </template>
        <v-list-item-title>{{ tag.name }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>
