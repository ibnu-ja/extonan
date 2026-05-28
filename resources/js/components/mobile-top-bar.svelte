<script lang="ts">
    import UserNav from '@/components/user-nav.svelte';

    let {
        title = '',
        scrolled = false,
        autoHide = false,
    }: {
        title?: string;
        scrolled?: boolean;
        autoHide?: boolean;
    } = $props();

    let lastScrollY = $state(0);
    let hidden = $state(false);

    $effect(() => {
        if (typeof window === 'undefined' || !autoHide) {
return;
}

        const onScroll = () => {
            const sy = window.scrollY;

            if (sy > lastScrollY && sy > 50) {
                hidden = true;
            } else if (sy < lastScrollY) {
                hidden = false;
            }

            lastScrollY = sy;
        };
        window.addEventListener('scroll', onScroll, { passive: true });

        return () => window.removeEventListener('scroll', onScroll);
    });
</script>

<header
	class="fixed top-0 left-0 right-0 z-50 flex h-16 items-center gap-2 px-4 transition-all duration-300"
	class:bg-background={scrolled}
	class:bg-transparent={!scrolled}
	class:border-b={scrolled}
    style={hidden ? 'transform: translateY(-100%);' : ''}
>
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
</header>
