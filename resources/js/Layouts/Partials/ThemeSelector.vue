<script setup lang="ts">
import { useUserStore } from '@/stores/userStore'
import { mdiPaletteSwatchVariant } from '@mdi/js'
import DarkBg from '@/assets/theme/dark.svg'
import LightBg from '@/assets/theme/light.svg'
import SystemBg from '@/assets/theme/system.svg'

const user = useUserStore()

const themes = [
  {
    name: 'Dark',
    value: 'dark',
    background: DarkBg,
  },
  {
    name: 'Light',
    value: 'light',
    background: LightBg,
  },
  {
    name: 'System',
    value: 'system',
    background: SystemBg,
  },
]
</script>

<template>
  <v-menu>
    <template #activator="{ props }">
      <v-btn
        :icon="mdiPaletteSwatchVariant"
        v-bind="props"
      />
    </template>

    <v-card width="250">
      <v-card-text>
        <v-item-group
          v-model="user.theme"
          selected-class="bg-primary"
        >
          <v-row>
            <v-col
              v-for="theme in themes"
              :key="theme.value"
              cols="12"
              class="py-2"
            >
              <v-item
                v-slot="{ selectedClass, toggle, isSelected }"
                :value="theme.value"
              >
                <v-card
                  variant="tonal"
                  :class="[selectedClass]"
                  dark
                  height="50"
                  :image="theme.background"
                  :style="isSelected ? '' : 'filter: grayscale(100%);'"
                  @click="toggle"
                >
                  <v-card-title>{{ theme.name }}</v-card-title>
                </v-card>
              </v-item>
            </v-col>
          </v-row>
        </v-item-group>
        <!--        <v-radio-group v-model="user.theme">-->
        <!--          <v-radio-->
        <!--            label="System"-->
        <!--            value="system"-->
        <!--          />-->
        <!--          <v-radio-->
        <!--            label="Dark"-->
        <!--            value="dark"-->
        <!--          />-->
        <!--          <v-radio-->
        <!--            label="Light"-->
        <!--            value="light"-->
        <!--          />-->
        <!--        </v-radio-group>-->
      </v-card-text>
    </v-card>
  </v-menu>
</template>

<style scoped>

</style>
