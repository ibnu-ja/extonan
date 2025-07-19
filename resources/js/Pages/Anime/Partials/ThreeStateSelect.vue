<script lang="ts" setup>
import { computed, ref } from 'vue'
import ThreeStateCheckbox from '@/Pages/Anime/Partials/ThreeStateCheckbox.vue'

// Define props including those that replace defineModel
const props = withDefaults(
  defineProps<{
    maxHeight?: number | string
    items: string[]
    shorten?: boolean
    shortenLength?: number
    // Replaces defineModel<string[]>() -> defaults to 'modelValue'
    modelValue: string[]
    // Replaces defineModel<string[]>('tagsNotIn')
    tagsNotIn: string[]
  }>(), {
    maxHeight: 400,
    items: () => [],
    shorten: true,
    shortenLength: 4,
    // Provide defaults for the new props
    modelValue: () => [],
    tagsNotIn: () => [],
  },
)

// Define the emits that correspond to the models being updated
const emit = defineEmits<{
  // Corresponds to updates for the 'modelValue' prop (was itemIn)
  (e: 'update:modelValue', value: string[]): void
  // Corresponds to updates for the 'tagsNotIn' prop (was itemNotIn)
  (e: 'update:tagsNotIn', value: string[]): void
}>()

type Item = {
  name: string
  state?: 'in' | 'notIn'
  active: boolean
  activeColor?: string
}

// --- Computed Properties and Refs ---

// Use props directly instead of model refs
const itemList = computed(() => {
  return props.items.map((item): Item => {
    let state: 'in' | 'notIn' | undefined
    let color
    // Read from props.modelValue instead of itemIn.value
    if (props.modelValue.includes(item)) {
      color = 'success'
      state = 'in'
      // Read from props.tagsNotIn instead of itemNotIn.value
    } else if (props.tagsNotIn.includes(item)) {
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

// Initialize selectedItems based on initial prop values
const selectedItems = ref<string[]>(props.items.filter((i) => {
  // Read from props.modelValue
  if (props.modelValue.includes(i)) {
    return true // Keep the item name as is
    // Read from props.tagsNotIn
  } else if (props.tagsNotIn.includes(i)) {
    // Note: The original logic added `-` prefix here, which seems inconsistent
    // with how selectedItems is used later. Sticking to the original logic for now,
    // but it might need review depending on the exact use case.
    // If selectedItems should only contain the raw names, remove the `-` prefix.
    // return `-${i}` // Original logic
    return true // Assuming selectedItems should just track which *original* items are selected
  }
  return false
}))

// Computed property to represent the selected items in the format needed by v-autocomplete
// This now reads directly from props to determine state/color
const bwang = computed((): Item[] => {
  // Map over the selectedItems ref, which tracks *which* items from the original list are selected
  return selectedItems.value.map((itemName): Item => {
    let state: 'in' | 'notIn' | undefined
    let color
    // Check the *props* to determine the current state ('in' or 'notIn')
    if (props.modelValue.includes(itemName)) {
      color = 'success'
      state = 'in'
    } else if (props.tagsNotIn.includes(itemName)) {
      color = 'error'
      state = 'notIn'
    }
    // If an item is in selectedItems but not in props.modelValue or props.tagsNotIn,
    // it implies an intermediate state or potential inconsistency.
    // The original code might have implicitly handled this.
    // Here, we default active to false if no state is found in props.
    return {
      active: color != undefined,
      activeColor: color,
      name: itemName,
      state: state,
    }
  }).filter(item => item.active) // Only include items that actually have a state ('in' or 'notIn')
})

// --- Methods ---

const customSearch = (itemTitle: string, queryText: string, item?: InternalItem<Item>) => {
  // Ensure item and item.raw are defined before accessing name
  if (!item || !item.raw) return false
  return item.raw.name.toLocaleLowerCase().indexOf(queryText.toLowerCase()) > -1
}

// Emit updates instead of modifying model refs
const removeTagIn = (tag: string) => {
  // Emit the update event with the new filtered array
  emit('update:modelValue', props.modelValue.filter(item => item !== tag))
}
const removeTagNot = (tag: string) => {
  // Emit the update event with the new filtered array
  emit('update:tagsNotIn', props.tagsNotIn.filter(item => item !== tag))
}

// removeTag now calls the specific remove functions which handle emits
const removeTag = (tag: string, state?: string) => {
  // Remove from the local tracking ref `selectedItems`
  selectedItems.value = selectedItems.value.filter(item => item !== tag)
  // Call the appropriate removal function based on state, which will emit the update
  if (state === 'notIn') {
    removeTagNot(tag)
  } else if (state === 'in') {
    removeTagIn(tag)
  }
  // Note: If state is undefined (e.g., item was somehow selected but had no state),
  // it won't be removed from the parent's modelValue or tagsNotIn arrays.
}

// itemClick now emits updates
const itemClick = (tag: string) => {
  // Read from props
  if (!props.modelValue.includes(tag) && !props.tagsNotIn.includes(tag)) {
    // State: None -> In
    selectedItems.value.push(tag) // Add to local selection tracking
    // Emit update for modelValue (itemIn)
    emit('update:modelValue', [...props.modelValue, tag]) // Create new array
  } else if (props.modelValue.includes(tag) && !props.tagsNotIn.includes(tag)) {
    // State: In -> NotIn
    // Remove from modelValue (itemIn) - emits update:modelValue
    removeTagIn(tag)
    // Add to tagsNotIn (itemNotIn) - emits update:tagsNotIn
    emit('update:tagsNotIn', [...props.tagsNotIn, tag]) // Create new array
    // selectedItems.value remains unchanged as the item is still selected, just state changes
  } else if (!props.modelValue.includes(tag) && props.tagsNotIn.includes(tag)) {
    // State: NotIn -> None
    // Remove from local selection tracking
    selectedItems.value = selectedItems.value.filter(item => item !== tag)
    // Remove from tagsNotIn (itemNotIn) - emits update:tagsNotIn
    removeTagNot(tag)
  }
}

// Handle backspace keydown
const handleOnKeydown = (e: KeyboardEventInit): void => {
  if (e.key === 'Backspace' && bwang.value.length > 0) {
    // Get the last item *currently displayed* based on the `bwang` computed property
    const lastItem = bwang.value[bwang.value.length - 1]
    if (lastItem) { // Ensure lastItem exists
      removeTag(lastItem.name, lastItem.state)
    }
  }
}

// Clear function would now look like this (if uncommented):
// const clear = () => {
//   selectedItems.value = []
//   emit('update:modelValue', [])
//   emit('update:tagsNotIn', [])
// }

</script>

<template>
  <v-autocomplete
    :model-value="bwang"
    multiple
    v-bind="$attrs"
    :items="itemList"
    :return-object="true"
    :custom-filter="customSearch"
    hide-details
    @keydown="handleOnKeydown"
  >
    <template #selection="{ item, index }">
      <v-chip
        v-if="shorten ? index < shortenLength : true"
        :key="item.raw.name"
        closable
        :color="item.raw.activeColor"
        @click.prevent
        @click:close.prevent="removeTag(item.raw.name, item.raw.state)"
      >
        {{ item.raw.name }}
      </v-chip>
      <div
        v-if="shorten && index === shortenLength && bwang.length > shortenLength"
        :key="'shorten-indicator'"
        class="text-grey text-caption align-self-center ml-1"
      >
        others (
        <span
          v-if="props.modelValue.length > 0"
          class="text-success"
        >+{{ props.modelValue.length }}</span> <span
          v-if="props.tagsNotIn.length > 0"
          class="text-error"
        >-{{ props.tagsNotIn.length }}</span>)
      </div>
    </template>

    <template #item="{ item }">
      <v-list-item
        :key="item.value.name"
        :value="item.value"
        :active="item.value.active"
        :color="item.value.activeColor"
        @click="itemClick(item.value.name)"
      >
        <template #prepend>
          <ThreeStateCheckbox
            :state="item.raw.state"
            class="mr-2"
          />
        </template>
        <v-list-item-title>{{ item.raw.name }}</v-list-item-title>
      </v-list-item>
    </template>
  </v-autocomplete>
</template>
