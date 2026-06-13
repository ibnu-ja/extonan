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
    let scrolled = $derived(y > threshold);
</script>

<header
    class="fixed top-0 left-0 right-0 z-50 flex h-16 items-center bg-transparent transition-opacity duration-300 opacity-0"
    class:opacity-100={scrolled}
>
    <div
        class="absolute inset-0 transition-opacity duration-300 opacity-0"
        class:opacity-100={scrolled}
        style="background: linear-gradient(to bottom, var(--background) 0%, color-mix(in srgb, var(--background) 80%, transparent) 50%, transparent 100%);"
    ></div>
    <div
        class="absolute inset-0 bg-background transition-opacity duration-300 opacity-0"
        class:opacity-100={scrolled}
    ></div>
    <div
        class="absolute inset-x-0 bottom-0 h-px bg-border transition-opacity duration-300 opacity-0"
        class:opacity-100={scrolled}
    ></div>
    <div class="relative flex flex-1 items-center gap-2 px-4 min-w-0">
        <h1
            class="flex-1 min-w-0 truncate text-base font-semibold font-heading"
        >
            {title}
        </h1>

        <UserNav />
    </div>
</header>
