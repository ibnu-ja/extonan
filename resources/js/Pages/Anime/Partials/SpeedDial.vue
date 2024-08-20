<script setup lang="ts">
import { mdiClose, mdiMoviePlus, mdiPlaylistPlus, mdiPlus } from '@mdi/js'
import { ref } from 'vue'
import InertiaLink from '@/Components/InertiaLink.vue'
import { VBtn } from 'vuetify/components'
import { AnimeData } from '@/types/anime'

const open = ref(false)

defineProps<{
  anime: AnimeData
}>()
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
      <InertiaLink
        key="2"
        :as="VBtn"
        color="secondary"
        :icon="true"
        :href="route('post.create', anime )"
      >
        <v-icon
          :icon="mdiMoviePlus"
        />

        <div class="fab-text-custom text-subtitle-2 font-weight-bold text-medium-emphasis">
          Add Episode
        </div>
      </InertiaLink>
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
