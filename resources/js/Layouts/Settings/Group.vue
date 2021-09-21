<template>
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
              active ? 'primary' : `grey ${dark ? 'darken' : 'lighten'}-3`
            "
            :dark="!dark && active"
            class="v-card--group py-3 px-4 text-center position-relative cursor-pointer d-flex align-center justify-space-between"
            rounded
            flat
            @click="toggle"
          >
            <i18n :path="text" />

            <v-icon v-text="icon" />
          </v-card>
        </template>
      </v-item>
    </v-col>
  </v-item-group>
</template>
