<template>
  <div
    ref="root"
    class="v-video relative w-full select-none focus:outline-none"
    tabindex="0"
    @mousemove="showControls"
    @keydown.space.prevent="togglePlayPause"
    @mouseleave="hideControls"
  >
    <!-- video element -->
    <video
      ref="video"
      class="v-video__media w-full block h-auto object-contain object-center"
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
      @click="togglePlayPause"
      @dblclick="toggleFullscreen"
    />

    <!-- controls: keep color in CSS but layout with Tailwind -->
    <v-sheet
      class="v-video__controls absolute left-2 right-2 bottom-2 flex items-center justify-between p-1 transition-all duration-300 ease-in-out backdrop-blur-md border"
      :elevation="2"
      rounded="xl"
      :class="[
        controlsVisible
          ? 'opacity-90 translate-y-0 pointer-events-auto'
          : 'opacity-0 translate-y-3 pointer-events-none',
        isFullscreen ? 'rounded-xl' : 'rounded-none'
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
          v-if="showVolume"
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

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { useTemplateRef } from 'vue'
import Hls, { Level } from 'hls.js'
import { mdiFullscreen, mdiFullscreenExit, mdiPause, mdiPlay, mdiVolumeHigh, mdiVolumeMute } from 'mdi-js-es'

type Props = {
  src: string
  poster?: string
  autoplay?: boolean
  muted?: boolean
  showVolume?: boolean
}

const props = withDefaults(
  defineProps<Props>(), {
    showVolume: true,
    muted: false,
    poster: undefined,
  },
)

const emit = defineEmits<{
  (e: 'play'): void
  (e: 'pause'): void
  (e: 'ended'): void
  (e: 'error', ev: Event): void
  (e: 'ready'): void
}>()

const root = ref<HTMLElement | null>(null)
const video = useTemplateRef<HTMLVideoElement>('video')
const isPlaying = ref(false)
const isMuted = ref(props.muted)
const controlsVisible = ref(true)
const duration = ref(0)
const currentTime = ref(0)
const seeking = ref(0)
const volume = ref((props.muted ? 0 : 100))
const qualities = ref<Level[]>([])
const currentQuality = ref(-1)
const hlsRef = ref<Hls | null>(null)
const isFullscreen = ref(false)

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
  v.paused ? v.play().catch(() => {}) : v.pause()
}

function toggleMute() {
  const v = video.value!
  v.muted = !v.muted
  isMuted.value = v.muted
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
  } else {
    document.exitFullscreen?.()
    isFullscreen.value = false
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

<style lang="scss" scoped>
.v-video {
  background: rgb(var(--v-theme-surface));
}

.v-video__controls {
  background: rgba(var(--v-theme-surface), 0.9);
  border-color: rgba(var(--v-theme-outline), 0.12);
  color: rgb(var(--v-theme-on-surface));

  :deep(.v-theme--dark) & {
    background: rgba(var(--v-theme-surface-variant), 0.85);
    border-color: rgba(var(--v-theme-outline), 0.2);
  }
}

:deep(.v-btn) {
  color: rgb(var(--v-theme-on-surface)) !important;

  &:hover {
    background: rgba(var(--v-theme-on-surface), 0.08);
  }

  .v-icon {
    color: rgb(var(--v-theme-on-surface));
  }
}

:deep(.v-slider) {
  .v-slider__track-fill {
    background: rgb(var(--v-theme-primary));
  }

  .v-slider__track-background {
    background: rgba(var(--v-theme-on-surface), 0.3);
  }

  .v-slider__thumb {
    background: rgb(var(--v-theme-primary));
    border: 2px solid rgb(var(--v-theme-surface));
  }
}

:deep(.v-select) {
  .v-field__input {
    color: rgb(var(--v-theme-on-surface));
    //font-size: 0.875rem;
  }
}
</style>
