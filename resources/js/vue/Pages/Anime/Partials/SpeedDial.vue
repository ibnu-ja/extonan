<script setup lang="ts">
import { mdiClose, mdiDelete, mdiMoviePlus, mdiPencil } from 'mdi-js-es'
import { inject, ref } from 'vue'
import { Link as InertiaLink } from '@inertiajs/vue3'
import { VBtn } from 'vuetify/components'
import { openConfirmationDialog } from '@/composables/useDialog'
import { router } from '@inertiajs/vue3'
import { route as ziggyRoute } from 'ziggy-js'

const open = ref(false)

const props = defineProps<{
  animeId: number
  canCreateEpisode: boolean
  canDelete: boolean
  canEdit: boolean
}>()

const route = inject('route') as typeof ziggyRoute
const deleteAnime = async () => {
  try {
    const confirmed = await openConfirmationDialog('Are you sure want to delete this item?')
    if (confirmed && props.animeId) {
      router.delete(route('anime.destroy', props.animeId))
    }
  } catch {
    //
  }
}
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
    <v-icon :icon="open ? mdiClose : mdiPencil" />
    <v-speed-dial
      v-model="open"
      location="top center"
      transition="slide-y-reverse-transition"
      activator="parent"
    >
      <v-btn
        key="1"
        :disabled="!canDelete"
        color="error"
        :icon="true"
        @click.prevent="deleteAnime"
      >
        <v-icon
          :icon="mdiDelete"
        />

        <div class="fab-text-custom text-subtitle-2 font-weight-bold text-medium-emphasis">
          Delete Anime
        </div>
      </v-btn>
      <InertiaLink
        key="2"
        :disabled="!canEdit"
        :href="route('anime.edit', animeId)"
        :as="VBtn"
        color="success"
        :icon="true"
      >
        <v-icon
          :icon="mdiPencil"
        />

        <div class="fab-text-custom text-subtitle-2 font-weight-bold text-medium-emphasis">
          Edit Anime
        </div>
      </InertiaLink>
      <InertiaLink
        v-if="canCreateEpisode"
        key="3"
        :as="VBtn"
        color="secondary"
        :icon="true"
        :href="route('post.create', animeId)"
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
