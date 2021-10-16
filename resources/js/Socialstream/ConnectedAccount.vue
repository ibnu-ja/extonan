<template>
  <div>
    <v-list>
      <v-list-item>
        <v-list-item-avatar>
          <v-icon size="50">
            mdi-{{ provider }}
          </v-icon>
        </v-list-item-avatar><v-list-item-content>
          <v-list-item-title
            v-text="provider.charAt(0).toUpperCase() + provider.slice(1)"
          />
          <v-list-item-subtitle v-if="createdAt !== null">
            Connected {{ createdAt }}
          </v-list-item-subtitle>
          <v-list-item-subtitle v-else>
            Not connected.
          </v-list-item-subtitle>
        </v-list-item-content>
        <v-spacer />
        <v-list-item-action>
          <slot name="action" />
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </div>
</template>

<script>
export default {
  props: {
    // eslint-disable-next-line vue/require-default-prop
    provider: String,
    createdAt: {
      type: String,
      default: null
    }
  }
}
</script>
