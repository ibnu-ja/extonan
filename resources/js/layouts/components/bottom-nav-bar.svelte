<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { Icon } from '@/components/icon';
    import { currentUrlState } from '@/lib/current-url.svelte';
    import { toUrl } from '@/lib/utils';
    import { home } from '@/routes';
    import { index as album } from '@/routes/album';
    import { index as anime } from '@/routes/anime';
    import { index as mv } from '@/routes/mv';

    const url = currentUrlState();

    function navClass(
        href: Parameters<typeof toUrl>[0],
        exactActive = false,
    ): string {
        const resolved = toUrl(href);
        const isActive = exactActive
            ? url.isCurrentUrl(resolved, url.currentUrl)
            : url.isCurrentUrl(resolved, url.currentUrl) ||
              url.isCurrentOrParentUrl(resolved, url.currentUrl);

        return isActive
            ? 'relative flex flex-col items-center gap-0.5 px-3 pb-1 pt-1.5 text-[0.75rem] font-semibold text-primary [&>.nav-icon]:bg-primary-container [&>.nav-icon]:text-on-primary-container'
            : 'relative flex flex-col items-center gap-0.5 px-3 pb-1 pt-1.5 text-[0.75rem] text-muted-foreground hover:text-foreground';
    }
</script>

<nav
    class="fixed bottom-0 left-0 right-0 z-50 flex h-16 items-center justify-around border-t bg-background md:hidden"
>
    <Link href={toUrl(home())} class={navClass(home(), true)}>
        <span
            class="nav-icon flex items-center justify-center rounded-full px-3 py-0.5 transition-colors"
        >
            <Icon
                lucide={() => import('lucide-svelte/icons/house')}
                tabler={() => import('@tabler/icons-svelte/icons/home')}
                hugeicons={() =>
                    import('@hugeicons/core-free-icons/Home01Icon')}
                phosphor={() => import('phosphor-svelte/lib/HouseIcon')}
                remixicon={() => import('remixicon-svelte/icons/home-line')}
                class="size-4"
            />
        </span>
        <span>Home</span>
    </Link>
    <Link href={toUrl(anime())} class={navClass(anime())}>
        <span
            class="nav-icon flex items-center justify-center rounded-full px-3 py-0.5 transition-colors"
        >
            <Icon
                lucide={() => import('lucide-svelte/icons/film')}
                tabler={() => import('@tabler/icons-svelte/icons/movie')}
                hugeicons={() =>
                    import('@hugeicons/core-free-icons/Film01Icon')}
                phosphor={() => import('phosphor-svelte/lib/FilmSlateIcon')}
                remixicon={() => import('remixicon-svelte/icons/film-line')}
                class="size-4"
            />
        </span>
        <span>Anime</span>
    </Link>
    <Link href={toUrl(album())} class={navClass(album())}>
        <span
            class="nav-icon flex items-center justify-center rounded-full px-3 py-0.5 transition-colors"
        >
            <Icon
                lucide={() => import('lucide-svelte/icons/disc-3')}
                tabler={() => import('@tabler/icons-svelte/icons/disc')}
                hugeicons={() => import('@hugeicons/core-free-icons/DiscIcon')}
                phosphor={() => import('phosphor-svelte/lib/DiscIcon')}
                remixicon={() => import('remixicon-svelte/icons/disc-line')}
                class="size-4"
            />
        </span>
        <span>Album</span>
    </Link>
    <Link href={toUrl(mv())} class={navClass(mv())}>
        <span
            class="nav-icon flex items-center justify-center rounded-full px-3 py-0.5 transition-colors"
        >
            <Icon
                lucide={() => import('lucide-svelte/icons/clapperboard')}
                tabler={() => import('@tabler/icons-svelte/icons/movie')}
                hugeicons={() =>
                    import('@hugeicons/core-free-icons/ClapperboardIcon')}
                phosphor={() => import('phosphor-svelte/lib/FilmReelIcon')}
                remixicon={() => import('remixicon-svelte/icons/movie-line')}
                class="size-4"
            />
        </span>
        <span>MV</span>
    </Link>
</nav>
