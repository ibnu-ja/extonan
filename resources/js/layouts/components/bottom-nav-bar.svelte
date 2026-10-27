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
            ? 'relative flex flex-col items-center gap-0.5 px-3 pb-1 pt-2 text-[0.75rem] text-primary'
            : 'relative flex flex-col items-center gap-0.5 px-3 pb-1 pt-2 text-[0.75rem] text-muted-foreground';
    }
</script>

<nav
    class="fixed bottom-0 left-0 right-0 z-50 flex h-16 items-center justify-around border-t bg-background md:hidden"
>
    <Link href={toUrl(home())} class={navClass(home(), true)}>
        <Icon
            lucide="HouseIcon"
            tabler="IconHome"
            hugeicons="Home01Icon"
            phosphor="HouseIcon"
            remixicon="RiHomeLine"
            class="size-4"
        />
        <span>Home</span>
    </Link>
    <Link href={toUrl(anime())} class={navClass(anime())}>
        <Icon
            lucide="FilmIcon"
            tabler="IconFilm"
            hugeicons="Film01Icon"
            phosphor="FilmSlateIcon"
            remixicon="RiFilmLine"
            class="size-4"
        />
        <span>Anime</span>
    </Link>
    <Link href={toUrl(album())} class={navClass(album())}>
        <Icon
            lucide="Disc3Icon"
            tabler="IconDisc"
            hugeicons="Disc02Icon"
            phosphor="DiscIcon"
            remixicon="RiDiscLine"
            class="size-4"
        />
        <span>Album</span>
    </Link>
    <Link href={toUrl(mv())} class={navClass(mv())}>
        <Icon
            lucide="ClapperboardIcon"
            tabler="IconClapperboard"
            hugeicons="ClapperboardIcon"
            phosphor="FilmReelIcon"
            remixicon="RiMovieLine"
            class="size-4"
        />
        <span>MV</span>
    </Link>
</nav>
