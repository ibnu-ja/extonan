<script module lang="ts">
    import { index as albumIndex } from '@/routes/album';

    export const layout = {
        breadcrumbs: [{ title: 'Album & Single', href: albumIndex() }],
    };
</script>

<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import { Icon } from '@/components/icon';
    import Pagination from '@/components/pagination.svelte';
    import PostCard from '@/components/post-card.svelte';
    import { Fab } from '@/components/ui/fab';
    import * as InputGroup from '@/components/ui/input-group';
    import * as Tabs from '@/components/ui/tabs';
    import {
        create as albumCreate,
        index as albumIndexRoute,
    } from '@/routes/album';

    let {
        albums,
        canCreate = false,
    }: App.Data.Music.AlbumIndexResponse = $props();

    let search = $state(
        typeof window !== 'undefined'
            ? (new URLSearchParams(window.location.search).get(
                  'filter[title]',
              ) ?? '')
            : '',
    );

    let currentType = $derived.by(() => {
        if (typeof window === 'undefined') {
            return 'all';
        }

        const params = new URLSearchParams(window.location.search);

        return params.get('filter[type]') ?? 'all';
    });

    let debounceTimer: ReturnType<typeof setTimeout> | undefined;

    function applyFilters(newType = currentType, newSearch = search) {
        const params = new URLSearchParams(window.location.search);

        if (newType && newType !== 'all') {
            params.set('filter[type]', newType);
        } else {
            params.delete('filter[type]');
        }

        const trimmed = newSearch.trim();

        if (trimmed) {
            params.set('filter[title]', trimmed);
        } else {
            params.delete('filter[title]');
        }

        params.delete('page');
        const query = params.toString();
        const url = albumIndexRoute().url + (query ? `?${query}` : '');
        router.get(
            url,
            {},
            { preserveState: true, preserveScroll: true, only: ['albums'] },
        );
    }

    function handleTabChange(value: string) {
        applyFilters(value, search);
    }

    function onSearchInput(value: string) {
        search = value;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            applyFilters(currentType, value);
        }, 300);
    }

    function clearSearch() {
        search = '';
        applyFilters(currentType, '');
    }
</script>

<svelte:head>
    <title>Albums & Singles</title>
</svelte:head>

<div class="space-y-6 p-4">
    <div
        class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4"
    >
        <Tabs.Root value={currentType} onValueChange={handleTabChange}>
            <Tabs.List>
                <Tabs.Trigger value="all">All</Tabs.Trigger>
                <Tabs.Trigger value="album">Albums</Tabs.Trigger>
                <Tabs.Trigger value="single">Singles</Tabs.Trigger>
            </Tabs.List>
        </Tabs.Root>

        <div class="w-full sm:w-72 md:w-80">
            <InputGroup.Root class="w-full">
                <InputGroup.Addon>
                    <Icon
                        lucide={() => import('lucide-svelte/icons/search')}
                        tabler={() => import('@tabler/icons-svelte/icons/search')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/Search01Icon')}
                        phosphor={() =>
                            import('phosphor-svelte/lib/MagnifyingGlassIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/search-line')}
                        class="text-muted-foreground size-4"
                    />
                </InputGroup.Addon>
                <InputGroup.Input
                    placeholder="Search title..."
                    bind:value={search}
                    name="search"
                    autocomplete="off"
                    oninput={(e) =>
                        onSearchInput((e.target as HTMLInputElement).value)}
                />
                {#if search}
                    <InputGroup.Addon align="inline-end">
                        <InputGroup.Button
                            variant="ghost"
                            size="icon-xs"
                            onclick={clearSearch}
                        >
                            <Icon
                                lucide={() => import('lucide-svelte/icons/x')}
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/x')}
                                hugeicons={() =>
                                    import(
                                        '@hugeicons/core-free-icons/Cancel01Icon'
                                    )}
                                phosphor={() =>
                                    import('phosphor-svelte/lib/XIcon')}
                                remixicon={() =>
                                    import('remixicon-svelte/icons/close-line')}
                                class="size-3"
                            />
                            <span class="sr-only">Clear</span>
                        </InputGroup.Button>
                    </InputGroup.Addon>
                {/if}
            </InputGroup.Root>
        </div>
    </div>

    {#if albums.data && albums.data.length > 0}
        <div
            class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6"
        >
            {#each albums.data as item (item.id)}
                <PostCard post={item as any} aspect="square" />
            {/each}
        </div>

        {#if albums.links && albums.links.length > 3}
            <Pagination data={albums as any} only={['albums']} />
        {/if}
    {:else}
        <div
            class="flex h-64 flex-col items-center justify-center rounded-[var(--radius)] border border-dashed border-border bg-muted/30 p-8 text-center"
        >
            <p class="text-lg font-medium text-muted-foreground">
                No albums or singles found.
            </p>
            <p class="text-sm text-muted-foreground/80 mt-1">
                New music releases will appear here once published.
            </p>
        </div>
    {/if}

    {#if canCreate}
        <Fab>
            {#snippet child({ props })}
                <Link href={albumCreate().url} prefetch="hover" {...props}>
                    <Icon
                        lucide={() => import('lucide-svelte/icons/plus')}
                        tabler={() => import('@tabler/icons-svelte/icons/plus')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/Add01Icon')}
                        phosphor={() => import('phosphor-svelte/lib/PlusIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/add-line')}
                        class="size-5"
                    />
                    <span class="sr-only">Create Album</span>
                </Link>
            {/snippet}
        </Fab>
    {/if}
</div>
