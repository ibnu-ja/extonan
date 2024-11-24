<script lang="ts" setup>

import { CoverImage } from '@/types/anilist'
import { useGradient } from '@/composables/useGradient'
import { Permissions } from '@/types'
import { TranslatableField } from '@/types/formHelper'
import { VCard } from 'vuetify/components'
import { useLanguages } from '@/composables/useLanguages'
import { useDisplay } from 'vuetify'
import { computed, ref } from 'vue'
import { mdiArrowRight, mdiClose } from 'mdi-js-es'

const { gradient } = useGradient()

defineProps<{
  permissions?: Permissions
  isPublished: boolean
  deleteUrl?: string
  editUrl?: string
  bg: string
  coverImage: CoverImage
  title: TranslatableField
  description: TranslatableField
}>()

const { translate, hasTranslation } = useLanguages()

const { xs, smAndUp } = useDisplay()

const mobileDescription = ref<HTMLParagraphElement>()

const isClamped = computed(() => {
  return mobileDescription.value!.scrollHeight > mobileDescription.value!.clientHeight
})

const showingSynopsis = ref(false)
</script>

<template>
  <v-card
    :rounded="false"
    class="flex relative elevation-0 border-0"
    :elevation="0"
    variant="text"
  >
    <template #image>
      <v-img
        :gradient
        class="bg-blur"
        cover
        :src="coverImage.medium"
      />
    </template>
    <v-container class="pb-0">
      <div class="sm:p-4 gap-4 flex sm:items-start flex-col sm:flex-row">
        <v-img
          style="cursor: pointer;"
          cover
          rounded
          width="200"
          class="flex-0-0 self-center sm:self-start"
          :src="coverImage.extraLarge"
          :lazy-src="coverImage.medium"
        >
          <v-dialog
            close-on-content-click
            fullscreen
            activator="parent"
          >
            <v-img
              height="100%"
              :src="coverImage.extraLarge"
              :lazy-src="coverImage.medium"
            />
          </v-dialog>
        </v-img>

        <div>
          <div class="flex align-top gap-4">
            <h3 class="text-h4 mb-2">
              {{ translate(title) }}
            </h3>
            <v-chip
              v-if="!isPublished"
              color="success"
              variant="flat"
            >
              Draft
            </v-chip>
          </div>
          <h3 class="text-h5 mb-2 text-medium-emphasis">
            {{ title.native }}
          </h3>
          <h3
            v-if="hasTranslation(title, false)"
            class="text-h5 mb-2 text-medium-emphasis"
          >
            {{ title.romaji }}
          </h3>
          <!--TODO render wysiwyg-->
          <p
            v-if="smAndUp"
            class="text-medium-emphasis text-subtitle-1 max-h-[200px] overflow-auto"
          >
            {{ translate(description) }}
          </p>
        </div>
      </div>
    </v-container>
  </v-card>
  <v-container
    v-if="xs"
    class="py-0"
  >
    <p
      ref="mobileDescription"
      class="text-medium-emphasis text-subtitle-1 line-clamp-4"
    >
      {{ translate(description) }}
    </p>

    <v-btn
      v-if="isClamped"
      :append-icon="mdiArrowRight"
      variant="text"
      color="primary"
      text="Read more"
      @click.prevent="showingSynopsis = true"
    />

    <v-dialog
      v-model="showingSynopsis"
    >
      <v-card>
        <v-card-item title="Summary">
          <template #append>
            <v-btn
              :icon="mdiClose"
              density="comfortable"
              variant="text"
              @click.prevent="showingSynopsis = false"
            />
          </template>
        </v-card-item>
        <v-card-text>
          <p>
            {{ translate(description) }}
          </p>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<style lang="scss">
.bg-blur {
  .v-img__img.v-img__img--cover {
    filter: blur(5px);
    transform: scale(1.05); //remove edge
  }
}
</style>
