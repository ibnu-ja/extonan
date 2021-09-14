<template>
  <label class="block font-medium text-sm text-gray-700">
    <span v-if="value">{{ value }}</span>
    <span v-else><slot /></span>
  </label>
</template>

<script>
export default {
  // eslint-disable-next-line vue/require-prop-types
  props: ['value']
}
</script>
