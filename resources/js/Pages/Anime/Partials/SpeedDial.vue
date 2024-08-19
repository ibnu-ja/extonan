<script setup lang="ts">
import { mdiClose, mdiMoviePlus, mdiPlaylistPlus, mdiPlus } from '@mdi/js'
import { ref } from 'vue'

const open = ref(false)
</script>
<template>
  <v-fab
    :active="!!$page.props.auth.user"
    app
    location="bottom right"
    size="large"
    color="primary"
    :icon="true"
  >
    <v-icon :icon="open ? mdiClose : mdiPlus" />
    <v-speed-dial
      v-model="open"
      location="top center"
      transition="slide-y-reverse-transition"
      activator="parent"
    >
      <v-btn
        key="1"
        color="success"
        :icon="true"
      >
        <v-icon
          :icon="mdiPlaylistPlus"
        />

        <div class="fab-text-custom text-subtitle-2 font-weight-bold text-medium-emphasis">
          Add Volume
        </div>
      </v-btn>
      <v-btn
        key="2"
        color="secondary"
        :icon="true"
      >
        <v-icon
          :icon="mdiMoviePlus"
        />

        <div class="fab-text-custom text-subtitle-2 font-weight-bold text-medium-emphasis">
          Add Episode
        </div>
      </v-btn>
    </v-speed-dial>
  </v-fab>
</template>

<style scoped lang="scss">
.fab-text-custom {
  position: absolute;
  right: 50px;
  padding: 10px;
}
</style>
