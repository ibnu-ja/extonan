<script lang="ts" setup>
import { inject, ref } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import FormSection from '@/Components/FormSection.vue'
import { User } from '@/types'
import { route as ziggyRoute } from 'ziggy-js'
import axios from 'axios'

const route = inject('route') as typeof ziggyRoute

const props = defineProps<{
  user: User
}>()

const page = usePage()

type Form = {
  _method: string
  name: string
  email: string
  photo: File | null
}

const form = useForm<Form>({
  _method: 'PUT',
  name: props.user.name,
  email: props.user.email,
  photo: null,
})

const verificationLinkSent = ref(false)
const photoPreview = ref<string | {
  readonly byteLength: number
  slice: (begin: number, end?: number | undefined) => ArrayBuffer
  readonly [Symbol.toStringTag]: string
} | null | undefined>(null)
const photoInput = ref<HTMLInputElement | null>(null)

const updateProfileInformation = () => {
  if (photoInput.value) {
    form.photo = photoInput.value.files![0]
  }

  form.post(route('user-profile-information.update'), {
    errorBag: 'updateProfileInformation',
    preserveScroll: true,
    onSuccess: () => clearPhotoFileInput(),
  })
}

const sendEmailVerification = () => {
  axios.post(route('verification.send'))
  verificationLinkSent.value = true
}

const selectNewPhoto = () => {
  photoInput.value?.click()
}

const updatePhotoPreview = () => {
  const photo = photoInput.value?.files?.[0]

  if (photo) return

  const reader = new FileReader()

  reader.onload = (e) => {
    photoPreview.value = e.target?.result
  }

  reader.readAsDataURL(photo)
}

const deletePhoto = () => {
  router.delete(route('current-user-photo.destroy'), {
    preserveScroll: true,
    onSuccess: () => {
      photoPreview.value = null
      clearPhotoFileInput()
    },
  })
}

const clearPhotoFileInput = () => {
  if (photoInput.value?.value) {
    photoInput.value = null
  }
}
</script>

<template>
  <FormSection @submitted="updateProfileInformation">
    <template #title>
      Profile Information
    </template>

    <template #description>
      Update your account's profile information and email address.
    </template>

    <template #form>
      <v-card-text>
        <!-- Profile Photo -->
        <div
          v-if="page.props.jetstream.managesProfilePhotos"
          class="mb-4"
        >
          <!-- Profile Photo File Input -->
          <input
            id="photo"
            ref="photoInput"
            type="file"
            class="d-none"
            @change="updatePhotoPreview"
          >
          <!-- Current & New Profile Photo Preview -->
          <v-avatar size="80">
            <v-img
              v-if="photoPreview"
              :transition="false"
              :src="(photoPreview as string)"
            />
            <v-img
              v-else
              :src="user.profile_photo_url!"
              :alt="user.name"
            />
          </v-avatar>

          <v-btn
            class="ml-3 mt-2 mr-2"
            variant="outlined"
            color="info"
            @click.prevent="selectNewPhoto"
          >
            Select A New Photo
          </v-btn>
          <v-btn
            v-if="user.profile_photo_path"
            class="mt-2"
            variant="outlined"
            color="error"
            @click.prevent="deletePhoto"
          >
            Remove Photo
          </v-btn>
        </div>

        <!-- Name -->
        <v-text-field
          v-model="form.name"
          label="Name"
          type="text"
          variant="outlined"
          autocomplete="name"
          :error-messages="form.errors.name"
          hide-details="auto"
          class="mb-4"
        />

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
          <v-text-field
            v-model="form.email"
            label="Email"
            type="email"
            variant="outlined"
            :error-messages="form.errors.email"
            autocomplete="username"
            class="mb-4"
            hide-details="auto"
          />

          <div v-if="page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
            <p class="text-sm mt-2">
              Your email address is unverified.
              <a
                :href="route('verification.send')"
                @click.prevent="sendEmailVerification"
              >
                Click here to re-send the verification email.
              </a>
            </p>

            <v-scroll-x-transition>
              <div
                v-show="verificationLinkSent"
                class="mt-2 text-success"
              >
                A new verification link has been sent to your email address.
              </div>
            </v-scroll-x-transition>
          </div>
        </div>
      </v-card-text>
    </template>

    <template #actions>
      <v-scroll-x-transition>
        <div v-show="form.recentlySuccessful">
          Saved.
        </div>
      </v-scroll-x-transition>

      <v-spacer />
      <v-btn
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="form.reset()"
      >
        Reset
      </v-btn>
      <v-btn
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        type="submit"
        color="primary"
      >
        Save
      </v-btn>
    </template>
  </FormSection>
</template>
