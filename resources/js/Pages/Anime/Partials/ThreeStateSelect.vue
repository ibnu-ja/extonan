<script lang="ts" setup>

import { computed, ref } from 'vue'
import ThreeStateCheckbox from '@/Pages/Anime/Partials/ThreeStateCheckbox.vue'

const props = withDefaults(
  defineProps<{
    maxHeight?: number | string
    items: string[]
    shorten?: boolean
    shortenLength?: number
  }>(), {
    maxHeight: 400,
    items: () => [],
    shorten: true,
    shortenLength: 4,
  },
)

type Item = {
  name: string
  state?: 'in' | 'notIn'
  active: boolean
  activeColor?: string
}

const itemIn = defineModel<string[]>({ default: () => [] })
// const tagsIn = ref<string[]>([])
const itemNotIn = defineModel<string[]>('tagsNotIn', { default: () => [] })
const itemList = computed(() => {
  return props.items.map((item): Item => {
    let state: 'in' | 'notIn' | undefined
    let color
    if (itemIn.value.includes(item)) {
      color = 'success'
      state = 'in'
    } else if (itemNotIn.value.includes(item)) {
      color = 'error'
      state = 'notIn'
    }

    return {
      active: color != undefined,
      activeColor: color,
      name: item,
      state: state,
    }
  })
})

interface InternalItem<T = unknown> {
  value: unknown
  raw: T
}

const customSearch = (itemTitle: string, queryText: string, item?: InternalItem<Item>) => {
  return item!.raw.name.toLocaleLowerCase().indexOf(queryText.toLowerCase()) > -1
}
const selectedItems = ref<string[]>([])

const bwang = computed(() => {
  return selectedItems.value.map((item): Item => {
    let state: 'in' | 'notIn' | undefined
    let color
    if (itemIn.value.includes(item)) {
      color = 'success'
      state = 'in'
    } else if (itemNotIn.value.includes(item)) {
      color = 'error'
      state = 'notIn'
    }

    return {
      active: color != undefined,
      activeColor: color,
      name: item,
      state: state,
    }
  })
})

const removeTagIn = (tag: string) => {
  itemIn.value = itemIn.value.filter(seasonItem => seasonItem != tag)
}
const removeTagNot = (tag: string) => {
  itemNotIn.value = itemNotIn.value.filter(seasonItem => seasonItem != tag)
}

const removeTag = (tag: string, state?: string) => {
  if (state === undefined) {
    return
  } else if (state === 'notIn') {
    selectedItems.value = selectedItems.value.filter(seasonItem => seasonItem != tag)
    removeTagNot(tag)
  } else if (state === 'in') {
    selectedItems.value = selectedItems.value.filter(seasonItem => seasonItem != tag)
    removeTagIn(tag)
  }
}

const itemClick = (tag: string) => {
  if (!itemIn.value.includes(tag) && !itemNotIn.value.includes(tag)) {
    selectedItems.value.push(tag)
    itemIn.value.push(tag)
  } else if (itemIn.value.includes(tag) && !itemNotIn.value.includes(tag)) {
    removeTagIn(tag)
    itemNotIn.value.push(tag)
  } else if (!itemIn.value.includes(tag) && itemNotIn.value.includes(tag)) {
    selectedItems.value = selectedItems.value.filter(seasonItem => seasonItem != tag)
    removeTagNot(tag)
  }
}

const clear = () => {
  itemIn.value = []
  itemNotIn.value = []
}

const handleOnKeydown = (e: KeyboardEventInit): void => {
  if (e.key === 'Backspace' && bwang.value.length > 0) {
    const lastItem = bwang.value[selectedItems.value.length - 1]
    removeTag(lastItem.name, lastItem.state)
  }
}

</script>

<template>
  <v-autocomplete
    :model-value="bwang"
    multiple
    v-bind="$attrs"
    :items="itemList"
    :return-object="true"
    :custom-filter="customSearch"
    @click:clear="clear"
    @keydown="handleOnKeydown"
  >
    <template #selection="{ item, index }">
      <v-chip
        v-if="index < shortenLength"
        :key="index"
        closable
        :color="item.value.activeColor"
        :value="item.value.name"
        @click.prevent
        @close.prevent="removeTag(item.value.name, item.value.state)"
        @click:close.prevent="removeTag(item.value.name, item.value.state)"
      >
        {{ item.value.name }}
      </v-chip>
      <div
        v-if="index === shortenLength"
        class="text-grey text-caption align-self-center"
      >
        <span
          v-if="itemIn.length>0"
          class="text-success"
        >+{{
          itemIn.length
        }}</span> <span
          v-if="itemNotIn.length>0"
          class="text-error"
        >-{{ itemNotIn.length }}</span> others
      </div>
    </template>
    <template #item="{ item, index }">
      <v-list-item
        :key="index"
        :value="item.value"
        :active="item.value.active"
        :color="item.value.activeColor"
        @click="itemClick(item.value.name)"
      >
        <template #prepend>
          <ThreeStateCheckbox :state="item.value.state" />
        </template>
        <v-list-item-title>{{ item.value.name }}</v-list-item-title>
      </v-list-item>
    </template>
  </v-autocomplete>
</template>
