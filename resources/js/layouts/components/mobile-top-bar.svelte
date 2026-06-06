<script lang="ts">
    import { scrollY } from 'svelte/reactivity/window';
    import UserNav from '@/layouts/components/user-nav.svelte';

    let {
        title = '',
        threshold = 60,
    }: {
        title?: string;
        threshold?: number;
    } = $props();

    let y = $derived(scrollY.current ?? 0);
    let showGradient = $derived(y > 0);
    let scrolled = $derived(y > threshold);
</script>

<header
    class="fixed top-0 left-0 right-0 z-50 flex h-16 items-center gap-2 px-4 bg-transparent"
>
    <div
        class="absolute inset-0 transition-opacity duration-300"
        style="opacity: {showGradient
            ? 1
            : 0}; background: linear-gradient(to bottom, var(--background) 0%, color-mix(in srgb, var(--background) 80%, transparent) 50%, transparent 100%);"
    ></div>
    <div
        class="absolute inset-0 bg-background transition-opacity duration-300"
        style="opacity: {scrolled ? 1 : 0};"
    ></div>
    <div
        class="absolute inset-x-0 bottom-0 h-px bg-border transition-opacity duration-300"
        style="opacity: {scrolled ? 1 : 0};"
    ></div>
    <div class="relative flex flex-1 items-center gap-2 px-4">
        <div class="flex flex-1 flex-col justify-center min-w-0">
            <h1
                class="transition-opacity duration-200 truncate text-base font-semibold font-heading"
                class:opacity-0={!scrolled}
                class:opacity-100={scrolled}
            >
                {title}
            </h1>
        </div>

        <UserNav />
    </div>
</header>
