<script lang="ts">

import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  layout: AppLayout,
}
</script>

<script lang="ts" setup>

import { AnimeData } from '@/types/anime'
import { Head, useForm } from '@inertiajs/vue3'
import { mdiDelete } from '@mdi/js/commonjs/mdi'
import { mdiContentSave, mdiSend } from '@mdi/js'
import PageHeader from '@/Layouts/Partials/PageHeader.vue'
import { useDisplay } from 'vuetify'
import { TranslatableField } from '@/types/formHelper'

defineProps<{
  anime: AnimeData
  canPublish: boolean
}>()

const { mdAndUp } = useDisplay()

type PostForm = {
  title: TranslatableField
  description: TranslatableField
  is_published: boolean
  metadata: unknown
}

const form = useForm<PostForm>({
  title: {
    en: null,
    id: null,
    native: null,
    romaji: null,
  },
  is_published: true,
  description: {
    en: null,
    id: null,
  },
  metadata: null,
},
)

const save = () => alert('bwang')
</script>

<template>
  <Head title="Add Episode" />
  <PageHeader title="Add Episode">
    <template #append>
      <div class="d-flex gap-2">
        <v-btn
          variant="outlined"
          color="error"
          :icon="!mdAndUp ? mdiDelete : undefined"
          :prepend-icon="mdAndUp ? mdiDelete : undefined"
          :text="mdAndUp ? 'Delete' : undefined"
        />
        <v-btn
          :variant="form.is_published ? undefined : 'outlined'"
          :type="canPublish && !form.is_published? undefined : 'submit'"
          form="storeAnime"
          :disabled="form.processing"
          :color=" form.is_published ? 'primary' : 'secondary'"
          :icon="!mdAndUp ? mdiContentSave : undefined"
          :prepend-icon="mdAndUp ? mdiContentSave : undefined"
          :text="mdAndUp ? 'Save' : undefined"
          @click.prevent="save"
        />
        <v-btn
          v-if="canPublish && !form.is_published"
          form="storeAnime"
          type="submit"
          :disabled="form.processing"
          color="primary"
          :icon="!mdAndUp ? mdiSend : undefined"
          :prepend-icon="mdAndUp ? mdiSend : undefined"
          :text="mdAndUp ? 'Publish' : undefined"
        />
      </div>
    </template>
  </PageHeader>
  <v-container>
    <v-container class="pa-0 pa-sm-4">
      <v-form>
        <v-row>
          <v-col
            cols="12"
            md="8"
          >
            <!--      -->
          </v-col>
          <v-col
            cols="12"
            md="4"
          >
            <!--  -->
          </v-col>
        </v-row>
      </v-form>
    </v-container>
  </v-container>
</template>
