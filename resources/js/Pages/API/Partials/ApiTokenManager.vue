<script setup lang="ts">
import { inject, ref, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import ActionSection from '@/Components/ActionSection.vue'
import FormSection from '@/Components/FormSection.vue'
import { chunk } from 'lodash-es'
import { ApiToken } from '@/types'
import { mdiContentCopy } from '@mdi/js'
import { UseClipboard } from '@vueuse/components'
import { route as ziggyRoute } from 'ziggy-js'

const page = usePage()

const route = inject('route') as typeof ziggyRoute

// tokens: Array,
// availablePermissions: Array,
// defaultPermissions: Array,
const props = defineProps<{
  tokens: ApiToken[]
  availablePermissions: string[]
  defaultPermissions: string[]
}>()

const createApiTokenForm = useForm({
  name: '',
  permissions: props.defaultPermissions,
})

const updateApiTokenForm = useForm({
  permissions: [] as string[],
})

const deleteApiTokenForm = useForm({})

const displayingToken = ref(false)
const managingPermissionsFor = ref<ApiToken | null>(null)
const apiTokenBeingManaged = ref(false)
const deletingApiTokenFor = ref<ApiToken | null>(null)
const apiTokenBeingDeleted = ref(false)

const createApiToken = () => {
  createApiTokenForm.post(route('api-tokens.store'), {
    preserveScroll: true,
    onSuccess: () => {
      displayingToken.value = true
      createApiTokenForm.reset()
    },
  })
}

const manageApiTokenPermissions = (token: ApiToken) => {
  apiTokenBeingManaged.value = true
  updateApiTokenForm.permissions = token.abilities
  managingPermissionsFor.value = token
}

const updateApiToken = () => {
  updateApiTokenForm.put(route('api-tokens.update', managingPermissionsFor.value!), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      managingPermissionsFor.value = null
      apiTokenBeingManaged.value = false
    },
  })
}

const chunked = ref(chunk(props.availablePermissions, 2))

const confirmApiTokenDeletion = (token: ApiToken) => {
  apiTokenBeingDeleted.value = true
  deletingApiTokenFor.value = token
}

const deleteApiToken = () => {
  deleteApiTokenForm.delete(route('api-tokens.destroy', deletingApiTokenFor.value!), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      apiTokenBeingDeleted.value = false
      deletingApiTokenFor.value = null
    },
  })
}

watch(apiTokenBeingManaged, async (newValue) => {
  if (!newValue) managingPermissionsFor.value = null
})
</script>

<template>
  <div>
    <!-- Generate API Token -->
    <FormSection @submitted="createApiToken">
      <template #title>
        Create API Token
      </template>

      <template #description>
        API tokens allow third-party services to authenticate with our application on your behalf.
      </template>

      <template #form>
        <v-card-text>
          <!-- Token Name -->
          <v-text-field
            v-model="createApiTokenForm.name"
            label="Name"
            type="text"
            variant="outlined"
            autocomplete="name"
            :error-messages="createApiTokenForm.errors.name"
            hide-details="auto"
          />

          <!-- Token Permissions -->

          <v-row dense>
            <v-col
              v-for="(item, key) in chunked"
              :key="key"
            >
              <v-checkbox
                v-for="permission in item"
                :key="permission"
                v-model="createApiTokenForm.permissions"
                hide-details="auto"
                :label="permission"
                :value="permission"
              />
            </v-col>
          </v-row>
        </v-card-text>
      </template>
      <template #actions>
        <v-scroll-x-transition>
          <div v-show="createApiTokenForm.recentlySuccessful">
            Created.
          </div>
        </v-scroll-x-transition>
        <v-spacer />
        <v-btn
          :disabled="createApiTokenForm.processing"
          type="submit"
        >
          Create
        </v-btn>
      </template>
    </FormSection>

    <template v-if="tokens.length > 0">
      <v-divider class="my-8" />

      <action-section>
        <template #title>
          Manage API Tokens
        </template>

        <template #description>
          You may delete any of your existing tokens if they are no longer needed.
        </template>

        <template #content>
          <v-list dense>
            <v-list-item
              v-for="token in tokens"
              :key="token.id"
            >
              <v-list-item-title>{{ token.name }}</v-list-item-title>
              <template #append>
                <v-btn
                  v-if="availablePermissions.length > 0"
                  small
                  class="mr-4"
                  variant="text"
                  @click.prevent="manageApiTokenPermissions(token)"
                >
                  Permissions
                </v-btn>
                <v-btn
                  v-if="availablePermissions.length > 0"
                  small
                  color="error"
                  variant="text"
                  @click.prevent="confirmApiTokenDeletion(token)"
                >
                  Delete
                </v-btn>
              </template>
            </v-list-item>
          </v-list>
        </template>
      </action-section>
    </template>

    <!-- Token Value Modal -->
    <v-dialog
      v-model="displayingToken"
      max-width="800"
    >
      <v-card>
        <v-card-title>
          API Token
        </v-card-title>

        <v-card-text>
          <p class="mb-4">
            Please copy your new API token. For your security, it won't be shown again.
          </p>
          <v-sheet
            v-if="page.props.jetstream.flash.token"
            class="mt-4 p-4"
            rounded="lg"
            style="position: relative; background-color: rgba(0, 0, 0, 0.1);"
          >
            <UseClipboard
              v-slot="{ copy, copied }"
              :source="page.props.jetstream.flash.token as string"
            >
              <v-tooltip
                location="left"
              >
                <template #activator="slot">
                  <v-btn
                    v-bind="slot.props"
                    variant="plain"
                    style="position: absolute; top: 4px; right: 4px"
                    :icon="mdiContentCopy"
                    @click="copy()"
                  />
                </template>

                <span v-text="copied ? 'Copied!': 'Copy Content'" />
              </v-tooltip>
            </UseClipboard>
            <pre v-text="page.props.jetstream.flash.token as string" />
          </v-sheet>
        </v-card-text>

        <v-card-actions>
          <v-btn
            @click="displayingToken = false"
          >
            Close
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="apiTokenBeingManaged"
      max-width="600"
    >
      <v-card>
        <v-card-title>
          API Token Permissions
        </v-card-title>

        <v-card-text>
          <v-row dense>
            <v-col
              v-for="(item, key) in chunked"
              :key="key"
            >
              <v-checkbox
                v-for="permission in item"
                :key="permission"
                v-model="updateApiTokenForm.permissions"
                :label="permission"
                :value="permission"
                hide-details
              />
            </v-col>
          </v-row>
        </v-card-text>

        <v-card-actions>
          <v-btn
            :disabled="updateApiTokenForm.processing"
            @click="apiTokenBeingManaged = false"
          >
            Cancel
          </v-btn>

          <v-btn
            :disabled="updateApiTokenForm.processing"
            type="submit"
            color="error"
            @click.prevent="updateApiToken"
          >
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Delete Token Confirmation Modal -->
    <v-dialog
      v-model="apiTokenBeingDeleted"
      max-width="600"
    >
      <v-card>
        <v-card-title>
          Delete API Token
        </v-card-title>

        <v-card-text>
          <p>
            Are you sure you would like to delete this API token?
          </p>
        </v-card-text>

        <v-card-actions>
          <v-btn
            :disabled="deleteApiTokenForm.processing"
            @click="apiTokenBeingDeleted = false"
          >
            Cancel
          </v-btn>

          <v-btn
            :disabled="deleteApiTokenForm.processing"
            type="submit"
            color="error"
            @click.prevent="deleteApiToken"
          >
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
