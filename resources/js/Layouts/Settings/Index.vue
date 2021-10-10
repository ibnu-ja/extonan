<template>
  <v-menu
    v-model="menu"
    :close-on-content-click="false"
    bottom
    center
    offset-y
    origin="top center"
    transition="scale-transition"
  >
    <template #activator="{ attrs, on }">
      <v-btn
        icon
        v-bind="attrs"
        class="mx-2"
        sm
        v-on="on"
      >
        <v-icon>
          mdi-cog-outline
        </v-icon>
      </v-btn>
    </template>

    <v-card class="py-2">
      <base-title
        align="center"
        title="Theme Colors"
        space="0"
      />

      <v-card-text>
        <v-item-group
          v-model="currentThemePrimary"
          class="d-flex justify-center"
        >
          <v-item
            v-for="color in colors[$vuetify.theme.isDark ? 'dark' : 'light']"
            :key="color"
            :value="color"
          >
            <template #default="{ active, toggle }">
              <v-avatar
                :color="color"
                :outlined="active"
                class="ma-2"
                size="48"
                style="cursor: pointer;"
                @click.stop="toggle"
              />
            </template>
          </v-item>
        </v-item-group>

        <v-divider class="my-6" />

        <v-item-group
          v-model="internalValue"
          class="mx-auto row row--dense"
          mandatory
        >
          <v-col
            v-for="{ icon, text } in items"
            :key="text"
            cols="6"
          >
            <v-item :value="text">
              <template #default="{ active, toggle }">
                <v-card
                  :color="
                    active
                      ? 'primary'
                      : `${dark ? 'primary darken-3' : 'grey lighten-3'}`
                  "
                  :dark="!dark && active"
                  class="v-card--group py-3 px-4 text-center position-relative cursor-pointer d-flex align-center justify-space-between"
                  rounded
                  flat
                  @click="toggle"
                >
                  {{ text }}
                  <v-icon v-text="icon" />
                </v-card>
              </template>
            </v-item>
          </v-col>
        </v-item-group>
      </v-card-text>
    </v-card>
  </v-menu>
</template>

<script>
import { sync } from 'vuex-pathify'
import BaseTitle from '@/Components/Base/Title'

export default {
  name: 'HomeSettings',

  components: {
    // eslint-disable-next-line vue/no-unused-components
    BaseTitle
  },

  data () {
    return {
      path: 'theme',
      colors: {
        dark: [this.$vuetify.theme.themes.dark.primary, '#9368e9', '#F4511E'],
        light: [this.$vuetify.theme.themes.light.primary, '#9368e9', '#F4511E']
      },
      menu: false,
      tes: 'test'
    }
  },

  computed: {
    ...sync('settings', ['theme@dark', 'theme@system']),
    ...sync('settings', {
      primaryDark: 'color@dark.primary',
      primaryLight: 'color@light.primary'
    }),
    // colorss: sync('settings/color'),
    items () {
      return [
        {
          text: 'light',
          icon: 'mdi-white-balance-sunny',
          cb: () => this.setTheme()
        },
        {
          text: 'dark',
          icon: 'mdi-weather-night',
          cb: () => this.setTheme(true)
        },
        {
          text: 'system',
          icon: 'mdi-desktop-tower-monitor',
          cb: () => this.setSystemTheme()
        }
      ]
    },
    currentThemePrimary: {
      get () {
        const target = this.$vuetify.theme.isDark ? 'dark' : 'light'
        return this.$vuetify.theme.themes[target].primary
      },
      set (val) {
        if (val) {
          const target = this.$vuetify.theme.isDark ? 'dark' : 'light'
          const color = this.$vuetify.theme.isDark
            ? 'primaryDark'
            : 'primaryLight'
          this.$vuetify.theme.themes[target].primary = val
          this[color] = val
          // this.colo[target].primary = val
        }
      }
    },
    internalValue: {
      get () {
        if (this.system) return 'system'
        return this.dark ? 'dark' : 'light'
      },
      set (val) {
        const set = this.items.find(item => item.text === val)
        set.cb()
      }
    }
  },
  watch: {
    '$vuetify.theme.isDark' (val) {
      if (this.dark === val) return
      this.dark = val
    },
    dark (val) {
      if (this.$vuetify.theme.dark === val) return
      this.$vuetify.theme.dark = val
    }
  },
  created () {
    const matchMedia = this.getMatchMedia()
    if (!matchMedia) return
    if (this.internalValue === 'system') {
      this.dark = matchMedia.matches
    }
    matchMedia.onchange = ({ matches }) => {
      if (this.system) {
        this.dark = matches
      }
    }
    this.$vuetify.theme.dark = this.dark
    this.$vuetify.theme.themes.dark.primary = this.primaryDark
    this.$vuetify.theme.themes.light.primary = this.primaryLight
  },
  methods: {
    getMatchMedia () {
      return window.matchMedia
        ? window.matchMedia('(prefers-color-scheme: dark)')
        : false
    },
    setTheme (dark = false, system = false) {
      this.dark = dark
      this.system = system
    },
    setSystemTheme () {
      const matchMedia = this.getMatchMedia()
      if (!matchMedia) return
      this.setTheme(matchMedia.matches, true)
    }
  }
}
</script>
