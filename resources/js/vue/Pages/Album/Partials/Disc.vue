<script lang="ts" setup>
import { computed, ref } from 'vue'
import { Disc } from '@/types/vgmdb'

const props = defineProps<{
  discs?: Disc[]
}>()

const activeLang = ref<string>('')

const trackLang = computed(() => {
  if (props.discs?.[0]?.tracks?.[0]?.names) {
    return Object.keys(props.discs[0].tracks[0].names)
  }
  return []
})

if (trackLang.value) {
  activeLang.value = trackLang.value[0]
}
</script>
<template>
  <section>
    <h4 class="text-h5 mb-4 px-2 sm:px-0">
      Tracklist
    </h4>
    <v-btn-toggle
      v-model="activeLang"
      class="my-2 text-right"
      mandatory
      borderless
      density="compact"
    >
      <v-btn
        v-for="(lang, i) in trackLang"
        :key="i"
        :value="lang"
        size="small"
      >
        {{ lang }}
      </v-btn>
    </v-btn-toggle>
    <v-expansion-panels
      multiple
    >
      <v-expansion-panel
        v-for="(disc, i) in discs"
        :id="'disc-'+i"
        :key="disc.name"
        static
      >
        <v-expansion-panel-title>
          {{ disc.name }}
        </v-expansion-panel-title>
        <v-expansion-panel-text
          class="disc"
        >
          <v-table density="compact">
            <thead>
              <tr>
                <th
                  class="text-left"
                  style="width: 10%"
                >
                  No.
                </th>
                <th class="text-left">
                  Title
                </th>
                <th class="text-right">
                  Duration
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(track, j) in disc.tracks"
                :key="j"
              >
                <td>{{ String(j + 1).padStart(2, '0') }}</td>
                <td>{{ track.names?.[activeLang] }}</td>
                <td class="text-right">
                  {{ track.track_length }}
                </td>
              </tr>
            </tbody>
          </v-table>
        </v-expansion-panel-text>
      </v-expansion-panel>
    </v-expansion-panels>
  </section>
</template>

<style>
div.v-expansion-panel-text.disc > div {
  padding:  0;
}
</style>
