<template>
  <div
    ref="root"
    class="v-video rounded-xl"
    tabindex="0"
    @mousemove="showControls"
    @keydown.space.prevent="togglePlayPause"
    @mouseleave="hideControls"
  >
    <video
      ref="video"
      class="v-video__media"
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
      @click.prevent="togglePlayPause"
      @dblclick.prevent="toggleFullscreen"
    />

    <v-sheet
      class="v-video__controls"
      elevation="2"
      :class="{ 'visible': controlsVisible }"
      tile
    >
      <div class="left">
        <v-btn
          variant="text"
          icon
          density="compact"
          @click="togglePlayPause"
        >
          <v-icon>{{ isPlaying ? mdiPause : mdiPlay }}</v-icon>
        </v-btn>

        <v-btn
          variant="text"
          icon
          density="compact"
          @click.prevent="toggleMute"
        >
          <v-icon>{{ isMuted ? mdiVolumeMute : mdiVolumeHigh }}</v-icon>
        </v-btn>

        <span class="time">{{ formatTime(currentTime) }} / {{ formatTime(duration) }}</span>
      </div>

      <div class="center mx-4">
        <v-slider
          v-model="seeking"
          color="primary"
          :step="0.1"
          density="compact"
          :max="duration || 1"
          hide-details
          @change="onSeekChange"
        />
      </div>

      <div class="right">
        <v-select
          v-if="qualities.length"
          v-model="currentQuality"
          variant="plain"
          density="compact"
          class="pb-2"
          hide-details
          :items="qualityOptions"
          :menu-props="{ attach: root as Element }"
          @update:model-value="onQualityChange"
        />
        <v-btn
          variant="text"
          icon
          density="compact"
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
import { useTheme } from 'vuetify'
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

const theme = useTheme()
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
  currentTime.value = video.value!.currentTime
  seeking.value = currentTime.value
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
  hideTimer = window.setTimeout(() => controlsVisible.value = false, 3000)
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
@use '../../css/settings';
@use 'sass:map';

// Use Vuetify settings for theming
.v-video {
  position: relative;
  background: rgb(var(--v-theme-surface));
  width: 100%;
  user-select: none;

  &__media {
    width: 100%;
    display: block;
    height: auto;
    background: rgb(var(--v-theme-surface));
    object-fit: contain;
    object-position: center center;
  }

  /* Fullscreen styles */
  &:fullscreen,
  &:-webkit-full-screen {
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgb(var(--v-theme-surface));
  }

  &:fullscreen .v-video__media,
  &:-webkit-full-screen .v-video__media {
    width: 100%;
    height: 100%;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    object-position: center center;
  }

  &__controls {
    position: absolute;
    left: 8px;
    right: 8px;
    bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 12px;
    background: rgba(var(--v-theme-surface), 0.9);
    color: rgb(var(--v-theme-on-surface));
    border-radius: settings.$border-radius-root;
    opacity: 0;
    transform: translateY(8px);
    transition: opacity 0.2s settings.$standard-easing,
    transform 0.2s settings.$standard-easing;
    pointer-events: none;
    backdrop-filter: blur(8px);
    border: thin solid rgba(var(--v-theme-outline), 0.12);

    // Dark theme specific adjustments
    :deep(.v-theme--dark) & {
      background: rgba(var(--v-theme-surface-variant), 0.85);
      border-color: rgba(var(--v-theme-outline), 0.2);
    }

    &.visible {
      opacity: 1;
      transform: translateY(0);
      pointer-events: auto;
    }

    .left, .center, .right {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .center {
      flex: 1;
    }

    .time {
      font-size: 0.875rem;
      font-weight: map.get(settings.$font-weights, 'regular');
      white-space: nowrap;
    }

    // Vuetify button overrides for video controls
    :deep(.v-btn) {
      color: rgb(var(--v-theme-on-surface)) !important;

      &:hover {
        background: rgba(var(--v-theme-on-surface), 0.08);
      }

      .v-icon {
        color: rgb(var(--v-theme-on-surface));
      }
    }

    // Slider theming
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

    // Select component theming
    :deep(.v-select) {
      .v-field__input {
        color: rgb(var(--v-theme-on-surface));
        font-size: 0.875rem;
      }
    }
  }
}

// Responsive design using Vuetify breakpoints
@media #{map.get(settings.$display-breakpoints, 'xs')} {
  .v-video__controls {
    .time {
      display: none;
    }

    .left, .right {
      gap: 4px;
    }

    padding: 4px 8px;
  }
}

// High contrast mode support
@media (prefers-contrast: high) {
  .v-video__controls {
    background: rgb(var(--v-theme-surface));
    border: 2px solid rgb(var(--v-theme-outline));

    .time {
      color: rgb(var(--v-theme-on-surface));
    }
  }
}

// Reduced motion support
@media (prefers-reduced-motion: reduce) {
  .v-video__controls {
    transition: opacity 0.1s ease, transform 0.1s ease;
  }
}
</style>
