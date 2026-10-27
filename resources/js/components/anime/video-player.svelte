<script lang="ts">
    import type Hls from 'hls.js';
    import type { Level } from 'hls.js';
    import { onMount, onDestroy } from 'svelte';
    import { Icon } from '@/components/icon';
    import { Button } from '@/components/ui/button';
    import * as Select from '@/components/ui/select';

    let {
        src,
        poster,
        autoplay = false,
        muted = false,
    }: {
        src: string;
        poster?: string;
        autoplay?: boolean;
        muted?: boolean;
    } = $props();

    let root: HTMLDivElement;
    let video: HTMLVideoElement;

    let isPlaying = $state(false);
    // svelte-ignore state_referenced_locally
    let isMuted = $state(muted);
    let duration = $state(0);
    let currentTime = $state(0);
    // svelte-ignore state_referenced_locally
    let volume = $state(muted ? 0 : 100);
    let levels: Level[] = $state([]);
    let hlsReady = $state(false);
    let isFullscreen = $state(false);
    let controlsVisible = $state(true);
    let hideTimer: number | undefined;
    let hls: Hls | null = null;
    let selectedQuality = $state('-1');

    const qualityOptions = $derived([
        { label: 'Auto', value: '-1' },
        ...levels.map((lvl, i) => ({
            label: lvl.height ? `${lvl.height}p` : `Level ${i}`,
            value: String(i),
        })),
    ]);

    function getQualityLabel(): string {
        if (selectedQuality === '-1') {
            return 'Auto';
        }

        const idx = parseInt(selectedQuality);
        const lvl = levels[idx];

        return lvl?.height ? `${lvl.height}p` : 'Auto';
    }

    function formatTime(sec: number): string {
        if (!sec || !isFinite(sec)) {
            return '0:00';
        }

        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = Math.floor(sec % 60);

        return h
            ? `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
            : `${m}:${String(s).padStart(2, '0')}`;
    }

    function togglePlayPause() {
        if (video.paused) {
            video.play().catch(() => {});
        } else {
            video.pause();
        }
    }

    function toggleMute() {
        video.muted = !video.muted;
        isMuted = video.muted;
    }

    function skipBack() {
        video.currentTime = Math.max(0, video.currentTime - 10);
    }

    function skipForward() {
        video.currentTime = Math.min(duration, video.currentTime + 10);
    }

    function onSeekChange(val: number[]) {
        video.currentTime = val[0];
    }

    function setQuality(val: string | undefined) {
        if (!hls || val === undefined) {
            return;
        }

        const level = parseInt(val);

        if (isNaN(level)) {
            return;
        }

        hls.currentLevel = level;
        selectedQuality = val;
    }

    function toggleFullscreen() {
        if (!isFullscreen) {
            root.requestFullscreen?.();
            isFullscreen = true;
            (
                screen.orientation as unknown as {
                    lock?: (o: string) => Promise<void>;
                }
            )
                ?.lock?.('landscape-primary')
                .catch(() => {});
        } else {
            document.exitFullscreen?.();
            isFullscreen = false;
            screen.orientation?.unlock?.();
        }
    }

    function showControlsFn() {
        controlsVisible = true;

        if (hideTimer) {
            clearTimeout(hideTimer);
        }

        hideTimer = window.setTimeout(() => (controlsVisible = false), 2000);
    }

    function hideControlsFn() {
        controlsVisible = false;
    }

    const seekPercent = $derived(duration ? (currentTime / duration) * 100 : 0);

    function onTimeUpdate() {
        if (video) {
            currentTime = video.currentTime;
        }
    }

    let isDestroyed = false;

    onMount(() => {
        video.volume = volume / 100;
        video.muted = isMuted;

        void (async () => {
            const { default: HlsClass } = await import('hls.js');

            if (isDestroyed || !video) {
                return;
            }

            if (HlsClass.isSupported()) {
                hls = new HlsClass({
                    enableWorker: true,
                    lowLatencyMode: false,
                    maxBufferLength: 30,
                    maxMaxBufferLength: 60,
                    startLevel: -1,
                });
                hls.attachMedia(video);
                hls.on(HlsClass.Events.MEDIA_ATTACHED, () =>
                    hls!.loadSource(src),
                );
                hls.on(HlsClass.Events.MANIFEST_PARSED, () => {
                    levels = hls!.levels;
                    hlsReady = true;

                    if (autoplay) {
                        video.play().catch(() => {});
                    }
                });

                hls.on(HlsClass.Events.ERROR, (_event, data) => {
                    if (data.fatal) {
                        switch (data.type) {
                            case HlsClass.ErrorTypes.NETWORK_ERROR:
                                hls?.startLoad();
                                break;
                            case HlsClass.ErrorTypes.MEDIA_ERROR:
                                hls?.recoverMediaError();
                                break;
                            default:
                                hls?.destroy();
                                break;
                        }
                    }
                });
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = src;
                video.addEventListener('loadedmetadata', () => {
                    hlsReady = true;

                    if (autoplay) {
                        video.play().catch(() => {});
                    }
                });
            } else {
                video.src = src;

                if (autoplay) {
                    video.play().catch(() => {});
                }
            }
        })();

        function onMouseMove() {
            showControlsFn();
        }

        function onMouseLeave() {
            hideControlsFn();
        }

        function onKeyDown(e: KeyboardEvent) {
            if (e.code === 'Space') {
                e.preventDefault();
                togglePlayPause();
            } else if (e.code === 'ArrowLeft') {
                e.preventDefault();
                skipBack();
            } else if (e.code === 'ArrowRight') {
                e.preventDefault();
                skipForward();
            }
        }

        root.setAttribute('tabindex', '0');
        root.addEventListener('mousemove', onMouseMove);
        root.addEventListener('mouseleave', onMouseLeave);
        root.addEventListener('keydown', onKeyDown);
        root.focus();

        return () => {
            hls?.destroy();
            root.removeEventListener('mousemove', onMouseMove);
            root.removeEventListener('mouseleave', onMouseLeave);
            root.removeEventListener('keydown', onKeyDown);
        };
    });

    $effect(() => {
        const currentSrc = src;

        if (hls && hlsReady && currentSrc) {
            hls.loadSource(currentSrc);
        } else if (video && !hls && currentSrc && video.src !== currentSrc) {
            video.src = currentSrc;
        }
    });

    onDestroy(() => {
        isDestroyed = true;
        hls?.destroy();

        if (hideTimer) {
            clearTimeout(hideTimer);
        }
    });
</script>

<div
    bind:this={root}
    class="relative w-full select-none bg-background"
    role="application"
>
    <video
        bind:this={video}
        class="w-full"
        class:h-full={isFullscreen}
        class:max-w-full={isFullscreen}
        class:object-contain={isFullscreen}
        class:rounded-none={!isFullscreen}
        class:sm:rounded-[var(--radius)]={!isFullscreen}
        playsinline
        {poster}
        muted={isMuted}
        preload="metadata"
        onloadedmetadata={() => (duration = video.duration)}
        ontimeupdate={onTimeUpdate}
        onplay={() => (isPlaying = true)}
        onpause={() => (isPlaying = false)}
        onclick={togglePlayPause}
        ondblclick={toggleFullscreen}
    ></video>

    <!-- Controls overlay -->
    <div
        class="absolute right-2 bottom-2 left-2 flex items-center justify-between rounded-[calc(var(--radius)-2px)] bg-background/90 p-1 shadow-lg backdrop-blur-sm transition-all duration-300 ease-in-out"
        class:opacity-90={controlsVisible}
        class:translate-y-0={controlsVisible}
        class:pointer-events-auto={controlsVisible}
        class:opacity-0={!controlsVisible}
        class:translate-y-3={!controlsVisible}
        class:pointer-events-none={!controlsVisible}
    >
        <div class="flex items-center gap-2">
            <Button
                variant="ghost"
                size="icon"
                class="size-8"
                onclick={togglePlayPause}
            >
                {#if isPlaying}
                    <Icon
                        lucide={() => import('lucide-svelte/icons/pause')}
                        tabler={() =>
                            import('@tabler/icons-svelte/icons/player-pause')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/PauseIcon')}
                        phosphor={() => import('phosphor-svelte/lib/PauseIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/pause-line')}
                        class="size-4"
                    />
                {:else}
                    <Icon
                        lucide={() => import('lucide-svelte/icons/play')}
                        tabler={() =>
                            import('@tabler/icons-svelte/icons/player-play')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/PlayIcon')}
                        phosphor={() => import('phosphor-svelte/lib/PlayIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/play-line')}
                        class="size-4"
                    />
                {/if}
            </Button>

            <Button
                variant="ghost"
                size="icon"
                class="size-8"
                onclick={toggleMute}
            >
                {#if isMuted || volume === 0}
                    <Icon
                        lucide={() => import('lucide-svelte/icons/volume-x')}
                        tabler={() =>
                            import('@tabler/icons-svelte/icons/volume-off')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/VolumeMute01Icon')}
                        phosphor={() =>
                            import('phosphor-svelte/lib/SpeakerSimpleSlashIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/volume-mute-line')}
                        class="size-4"
                    />
                {:else}
                    <Icon
                        lucide={() => import('lucide-svelte/icons/volume-2')}
                        tabler={() =>
                            import('@tabler/icons-svelte/icons/volume')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/VolumeHighIcon')}
                        phosphor={() =>
                            import('phosphor-svelte/lib/SpeakerHighIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/volume-up-line')}
                        class="size-4"
                    />
                {/if}
            </Button>

            <span
                class="text-muted-foreground hidden whitespace-nowrap text-xs sm:inline"
            >
                {formatTime(currentTime)} / {formatTime(duration)}
            </span>
        </div>

        <div class="mx-4 flex-1">
            <div class="relative flex h-1.5 w-full items-center">
                <div
                    class="bg-muted relative h-1.5 w-full overflow-hidden rounded-full"
                >
                    <div
                        class="bg-primary absolute h-full rounded-full"
                        style="width: {seekPercent}%"
                    ></div>
                </div>
                <input
                    type="range"
                    min="0"
                    max={duration || 1}
                    step="0.1"
                    value={currentTime}
                    oninput={(e) =>
                        onSeekChange([parseFloat(e.currentTarget.value)])}
                    class="absolute h-full w-full cursor-pointer appearance-none bg-transparent [&::-webkit-slider-thumb]:size-4 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:border [&::-webkit-slider-thumb]:border-primary [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:shadow-sm [&::-webkit-slider-thumb]:transition-[color,box-shadow] [&::-webkit-slider-thumb]:hover:ring-4 [&::-webkit-slider-thumb]:hover:ring-ring/50 [&::-moz-range-thumb]:size-4 [&::-moz-range-thumb]:appearance-none [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border [&::-moz-range-thumb]:border-primary [&::-moz-range-thumb]:bg-white [&::-moz-range-thumb]:shadow-sm"
                />
            </div>
        </div>

        <div class="flex items-center gap-2">
            <Button
                variant="ghost"
                size="icon"
                class="size-8"
                onclick={skipBack}
            >
                <Icon
                    lucide={() => import('lucide-svelte/icons/rewind')}
                    tabler={() =>
                        import('@tabler/icons-svelte/icons/player-track-prev')}
                    hugeicons={() =>
                        import('@hugeicons/core-free-icons/Backward01Icon')}
                    phosphor={() => import('phosphor-svelte/lib/RewindIcon')}
                    remixicon={() =>
                        import('remixicon-svelte/icons/rewind-line')}
                    class="size-4"
                />
            </Button>

            <Button
                variant="ghost"
                size="icon"
                class="size-8"
                onclick={skipForward}
            >
                <Icon
                    lucide={() => import('lucide-svelte/icons/fast-forward')}
                    tabler={() =>
                        import('@tabler/icons-svelte/icons/player-track-next')}
                    hugeicons={() =>
                        import('@hugeicons/core-free-icons/Forward01Icon')}
                    phosphor={() =>
                        import('phosphor-svelte/lib/FastForwardIcon')}
                    remixicon={() =>
                        import('remixicon-svelte/icons/speed-up-line')}
                    class="size-4"
                />
            </Button>

            {#if hlsReady && levels.length > 1}
                <Select.Root
                    type="single"
                    value={selectedQuality}
                    onValueChange={setQuality}
                >
                    <Select.Trigger
                        class="h-8 w-auto gap-1 border-0 bg-transparent px-2 text-xs"
                    >
                        {getQualityLabel()}
                    </Select.Trigger>
                    <Select.Content>
                        {#each qualityOptions as option (option.value)}
                            <Select.Item value={option.value}
                                >{option.label}</Select.Item
                            >
                        {/each}
                    </Select.Content>
                </Select.Root>
            {/if}

            <Button
                variant="ghost"
                size="icon"
                class="size-8"
                onclick={toggleFullscreen}
            >
                {#if isFullscreen}
                    <Icon
                        lucide={() => import('lucide-svelte/icons/minimize')}
                        tabler={() =>
                            import('@tabler/icons-svelte/icons/minimize')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/MinimizeScreenIcon')}
                        phosphor={() =>
                            import('phosphor-svelte/lib/ArrowsInIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/fullscreen-exit-line')}
                        class="size-4"
                    />
                {:else}
                    <Icon
                        lucide={() => import('lucide-svelte/icons/maximize')}
                        tabler={() =>
                            import('@tabler/icons-svelte/icons/maximize')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/MaximizeScreenIcon')}
                        phosphor={() =>
                            import('phosphor-svelte/lib/ArrowsOutIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/fullscreen-line')}
                        class="size-4"
                    />
                {/if}
            </Button>
        </div>
    </div>
</div>
