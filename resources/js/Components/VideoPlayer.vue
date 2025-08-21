<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, useTemplateRef } from 'vue'
import Hls, { Level } from 'hls.js'
import {
  mdiFullscreen,
  mdiFullscreenExit,
  mdiPause,
  mdiPlay,
  mdiSkipBackward,
  mdiSkipForward,
  mdiVolumeHigh,
  mdiVolumeMute,
} from 'mdi-js-es'
import { useScreenOrientation } from '@vueuse/core'
import { useDisplay } from 'vuetify'
import { useUserStore } from '@/stores'
import { storeToRefs } from 'pinia'

type Props = {
  src: string
  poster?: string
  autoplay?: boolean
  muted?: boolean
  showVolume?: boolean | 'auto'
  showSkipButtons?: boolean | 'auto'
}

const props = withDefaults(
  defineProps<Props>(), {
    showVolume: 'auto',
    muted: false,
    poster: undefined,
    showSkipButtons: 'auto',
  },
)

const { lockOrientation, unlockOrientation } = useScreenOrientation()

const emit = defineEmits<{
  (e: 'play'): void
  (e: 'pause'): void
  (e: 'ended'): void
  (e: 'error', ev: Event): void
  (e: 'ready'): void
}>()

const user = useUserStore()

const root = ref<HTMLDivElement | null>(null)
const video = useTemplateRef<HTMLVideoElement>('video')
const isPlaying = ref(false)
const isMuted = ref(props.muted)
const controlsVisible = ref(true)
const duration = ref(0)
const currentTime = ref(0)
const seeking = ref(0)
const volume = ref((props.muted ? 0 : 100))
const qualities = ref<Level[]>([])
const { videoQuality: currentQuality } = storeToRefs(user)
const hlsRef = ref<Hls | null>(null)
const isFullscreen = ref(false)
const { mdAndUp } = useDisplay()

const qualityOptions = computed(() => [
  { title: 'Auto', value: -1 },
  ...qualities.value.map((lvl, i) => ({
    title: lvl.height ? `${lvl.height}p` : `Level ${i}`, value: i,
  })),
])

function formatTime(sec: number) {
  if (!sec || !isFinite(sec)) return '0:00'
  const h = Math.floor(sec / 3600)
  const m = Math.floor((sec % 3600) / 60)
  const s = Math.floor(sec % 60)
  return h
    ? `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
    : `${m}:${String(s).padStart(2, '0')}`
}

function togglePlayPause() {
  const v = video.value!
  // eslint-disable-next-line @typescript-eslint/no-unused-expressions
  v.paused
    ? v.play().catch(() => {
      })
    : v.pause()
}

function toggleMute() {
  const v = video.value!
  v.muted = !v.muted
  isMuted.value = v.muted
}

function skipBack() {
  const v = video.value!
  v.currentTime = Math.max(0, v.currentTime - 10)
}

function skipForward() {
  const v = video.value!
  v.currentTime = Math.min(duration.value, v.currentTime + 10)
}

function onSeekChange() {
  video.value!.currentTime = seeking.value
}

function onLoadedMetadata() {
  duration.value = video.value!.duration
  emit('ready')
}

function onTimeUpdate() {
  if (video.value) {
    currentTime.value = video.value.currentTime
    seeking.value = currentTime.value
  }
}

function onPlay() {
  isPlaying.value = true
  emit('play')
}

function onPause() {
  isPlaying.value = false
  emit('pause')
}

function onEnded() {
  emit('ended')
}

function onError(e: Event) {
  emit('error', e)
}

function onQualityChange(val: number) {
  const hls = hlsRef.value!
  currentQuality.value = val
  hls.currentLevel = val
}

function toggleFullscreen() {
  const el = root.value!
  if (!isFullscreen.value) {
    el.requestFullscreen?.()
    isFullscreen.value = true
    lockOrientation('landscape-primary')
  } else {
    document.exitFullscreen?.()
    isFullscreen.value = false
    unlockOrientation()
  }
}

let hideTimer: number | null = null

function showControls() {
  controlsVisible.value = true
  if (hideTimer) clearTimeout(hideTimer)
  hideTimer = window.setTimeout(() => controlsVisible.value = false, 2000)
}

function hideControls() {
  controlsVisible.value = false
}

// TODO create button overlay skip backward 10s
function onVideoClick() {
  // event: MouseEvent
  // const rect = video.value!.getBoundingClientRect()
  // const clickX = event.clientX - rect.left
  // const videoWidth = rect.width
  //
  // // If clicked on the left side (first third), skip back
  // if (clickX < videoWidth / 3) {
  //   skipBack()
  // } else {
  togglePlayPause()
  // }
}

const computedShowSkipButtons = computed(() => {
  if (typeof props.showSkipButtons === 'boolean') {
    return props.showSkipButtons
  }
  return mdAndUp.value || isFullscreen.value
})

const computedShowVolume = computed(() => {
  if (typeof props.showVolume === 'boolean') {
    return props.showVolume
  }
  return mdAndUp.value
})

onMounted(() => {
  const v = video.value!
  v.volume = volume.value / 100
  v.muted = isMuted.value

  if (Hls.isSupported()) {
    const hls = new Hls()
    hlsRef.value = hls
    hls.attachMedia(v)
    hls.on(Hls.Events.MEDIA_ATTACHED, () => hls.loadSource(props.src))
    hls.on(Hls.Events.MANIFEST_PARSED, () => {
      qualities.value = hls.levels
      if (props.autoplay) v.play().catch(() => {})
    })
  } else {
    v.src = props.src
    if (props.autoplay) v.play().catch(() => {})
  }

  root.value?.focus()
  showControls()
})

onBeforeUnmount(() => {
  hlsRef.value?.destroy()
  if (hideTimer) clearTimeout(hideTimer)
})
</script>

<template>
  <div
    ref="root"
    class="relative w-full select-none focus:outline-none bg-background"
    tabindex="0"
    @mousemove="showControls"
    @keydown.space.prevent="togglePlayPause"
    @keydown.left.prevent="skipBack"
    @mouseleave="hideControls"
    @keydown.right.prevent="skipForward"
  >
    <!-- video element -->
    <video
      ref="video"
      class="w-full block h-auto object-contain object-center"
      :class="{ 'w-full h-full max-w-full max-h-full': isFullscreen, 'rounded-none sm:rounded-xl' : !isFullscreen }"
      playsinline
      :poster="poster"
      :muted="isMuted"
      preload="metadata"
      @loadedmetadata="onLoadedMetadata"
      @timeupdate="onTimeUpdate"
      @play="onPlay"
      @pause="onPause"
      @ended="onEnded"
      @error="onError"
      @click="onVideoClick"
      @dblclick="toggleFullscreen"
    />
    <!-- controls: keep color in CSS but layout with Tailwind -->
    <v-sheet
      class="absolute left-2 right-2 bottom-2 flex items-center justify-between p-1 transition-all duration-300 ease-in-out"
      rounded="xl"
      :elevation="isFullscreen ? 0 : 2"
      :class="[
        controlsVisible
          ? 'opacity-90 translate-y-0 pointer-events-auto'
          : 'opacity-0 translate-y-3 pointer-events-none',
      ]"
    >
      <div class="left flex items-center gap-2">
        <v-btn
          variant="text"
          icon
          class="!text-inherit"
          @click="togglePlayPause"
        >
          <v-icon>{{ isPlaying ? mdiPause : mdiPlay }}</v-icon>
        </v-btn>

        <v-btn
          v-if="computedShowVolume"
          variant="text"
          icon
          class="!text-inherit"
          @click.prevent="toggleMute"
        >
          <v-icon>{{ isMuted ? mdiVolumeMute : mdiVolumeHigh }}</v-icon>
        </v-btn>

        <span class="time font-normal whitespace-nowrap hidden sm:inline-block">
          {{ formatTime(currentTime) }} / {{ formatTime(duration) }}
        </span>
      </div>

      <div class="center flex-1 mx-4">
        <v-slider
          v-model="seeking"
          :step="0.1"
          :max="duration || 1"
          hide-details
          @update:model-value="onSeekChange"
        />
      </div>

      <div class="right flex items-center gap-2">
        <template v-if="computedShowSkipButtons">
          <v-btn
            variant="text"
            :icon="mdiSkipBackward"
            class="!text-inherit"
            title="Skip backward 10 seconds"
            @click="skipBack"
          />
          <v-btn
            variant="text"
            :icon="mdiSkipForward"
            class="!text-inherit"
            title="Skip forward 10 seconds"
            @click="skipForward"
          />
        </template>

        <v-select
          v-if="qualities.length"
          v-model="currentQuality"
          variant="plain"
          class="pb-4"
          hide-details
          :items="qualityOptions"
          :menu-props="{ attach: root as Element }"
          @update:model-value="onQualityChange"
        />
        <v-btn
          variant="text"
          icon
          class="text-inherit"
          @click="toggleFullscreen"
        >
          <v-icon>{{ isFullscreen ? mdiFullscreenExit : mdiFullscreen }}</v-icon>
        </v-btn>
      </div>
    </v-sheet>
  </div>
</template>
