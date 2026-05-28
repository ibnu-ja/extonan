<script module lang="ts">
    import { index as animeIndex } from '@/routes/anime';

    export const layout = {
        breadcrumbs: [{ title: 'Anime', href: animeIndex() }],
    };
</script>

<script lang="ts">
    import { page, router } from '@inertiajs/svelte';
    import { Clapperboard, Tag, Calendar, Eye, EyeOff } from 'lucide-svelte';
    import { Button } from '@/components/ui/button';
    import * as ButtonGroup from '@/components/ui/button-group';
    import { useAnime } from '@/lib/use-anime.svelte';
    import type { AnimeFilterState } from '@/lib/use-anime.svelte';
    import type { RouteQueryOptions } from '@/wayfinder';
    import FilterBar from './index/components/filter-bar.svelte';
    import FilterDropdown from './index/components/filter-dropdown.svelte';
    import Grid from './index/components/grid.svelte';
    import Pagination from './index/components/pagination.svelte';

    type PaginationData = {
        currentPage: number;
        lastPage: number;
        perPage: number;
        total: number;
        links: { url: string | null; label: string; active: boolean }[];
    };

    type AnimeItem = {
        id: number;
        title: Record<string, string | null>;
        slug: Record<string, string | null>;
        coverImage: {
            extraLarge: string;
            large: string;
            medium: string;
            color: string;
        };
        isPublished: boolean;
        link: string;
        permissions: { update: boolean; delete: boolean; publish: boolean };
    };

    const props = $derived(page.props as Record<string, unknown>);
    const items = $derived((props.items as AnimeItem[]) ?? []);
    const pagination = $derived(props.pagination as PaginationData | undefined);

    const { genres, tags, seasons, buildFilterQuery } = useAnime();
    const sortOptions = $derived(
        (page.props.sortOptions as { key: string; value: string }[]) ?? [],
    );

    let searchTimeout: ReturnType<typeof setTimeout> | undefined;

    function parseFilterState(): AnimeFilterState {
        const params = new URL(
            page.url,
            typeof window !== 'undefined'
                ? window.location.origin
                : 'http://localhost',
        ).searchParams;
        const state: AnimeFilterState = {
            title: '',
            genreIn: [],
            genreNotIn: [],
            tagIn: [],
            tagNotIn: [],
            seasonIn: [],
            seasonNotIn: [],
        };

        for (const [key, value] of params) {
            const match = key.match(/^filter\[(\w+)]\[\]$/);

            if (match) {
                const filterKey = match[1] as keyof AnimeFilterState;

                if (filterKey in state && Array.isArray(state[filterKey])) {
                    (state[filterKey] as string[]).push(value);
                }
            }
        }

        const title = params.get('filter[title]');

        if (title) {
            state.title = title;
        }

        return state;
    }

    let filters = $state<AnimeFilterState>(parseFilterState());
    let sort = $state(
        new URL(
            page.url,
            typeof window !== 'undefined'
                ? window.location.origin
                : 'http://localhost',
        ).searchParams.get('sort') || 'title->romaji',
    );
    const auth = $derived(
        page.props.auth as { permissions?: string[]; roles?: string[] },
    );
    const canReadDrafts = $derived(
        auth?.permissions?.includes('post.read.self') ?? false,
    );
    const filterIsPublished = $derived(
        new URL(
            page.url,
            typeof window !== 'undefined'
                ? window.location.origin
                : 'http://localhost',
        ).searchParams.get('filter[is_published]'),
    );
    let publishFilter = $state<'published' | 'draft' | null>(
        filterIsPublished === 'true'
            ? 'published'
            : filterIsPublished === 'false'
              ? 'draft'
              : null,
    );

    function applyFilters() {
        const query: Record<string, unknown> = {};
        const filter = buildFilterQuery(filters);

        if (Object.keys(filter).length > 0) {
            query.filter = filter;
        }

        if (sort) {
            query.sort = sort;
        }

        if (publishFilter) {
            query.filter = {
                ...(query.filter as Record<string, unknown>),
                is_published: publishFilter === 'published' ? true : false,
            };
        }

        router.get(animeIndex.url({ query } as RouteQueryOptions), undefined, {
            preserveState: true,
            preserveScroll: true,
        });
    }

    function onSearchChange(value: string) {
        filters.title = value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 400);
    }

    function onGenreSelect(item: string, mode: 'in' | 'notIn' | 'off') {
        if (mode === 'in') {
            filters.genreIn = [...filters.genreIn, item];
            filters.genreNotIn = filters.genreNotIn.filter((g) => g !== item);
        } else if (mode === 'notIn') {
            filters.genreNotIn = [...filters.genreNotIn, item];
            filters.genreIn = filters.genreIn.filter((g) => g !== item);
        } else {
            filters.genreIn = filters.genreIn.filter((g) => g !== item);
            filters.genreNotIn = filters.genreNotIn.filter((g) => g !== item);
        }
    }

    function onTagSelect(item: string, mode: 'in' | 'notIn' | 'off') {
        if (mode === 'in') {
            filters.tagIn = [...filters.tagIn, item];
            filters.tagNotIn = filters.tagNotIn.filter((t) => t !== item);
        } else if (mode === 'notIn') {
            filters.tagNotIn = [...filters.tagNotIn, item];
            filters.tagIn = filters.tagIn.filter((t) => t !== item);
        } else {
            filters.tagIn = filters.tagIn.filter((t) => t !== item);
            filters.tagNotIn = filters.tagNotIn.filter((t) => t !== item);
        }
    }

    function onSeasonSelect(item: string, mode: 'in' | 'notIn' | 'off') {
        if (mode === 'in') {
            filters.seasonIn = [...filters.seasonIn, item];
            filters.seasonNotIn = filters.seasonNotIn.filter((s) => s !== item);
        } else if (mode === 'notIn') {
            filters.seasonNotIn = [...filters.seasonNotIn, item];
            filters.seasonIn = filters.seasonIn.filter((s) => s !== item);
        } else {
            filters.seasonIn = filters.seasonIn.filter((s) => s !== item);
            filters.seasonNotIn = filters.seasonNotIn.filter((s) => s !== item);
        }
    }
</script>

<svelte:head>
    <title>Anime Index</title>
</svelte:head>

<div class="space-y-4 p-4">
    <FilterBar
        bind:search={filters.title}
        {sort}
        {sortOptions}
        onsortchange={(v) => {
            sort = v;
            applyFilters();
        }}
        onsearchchange={onSearchChange}
    />

    <div class="flex flex-wrap items-center gap-2">
        <FilterDropdown
            label="Genre"
            icon={Clapperboard}
            items={genres.map((g) => g.value)}
            selectedIn={filters.genreIn}
            selectedNotIn={filters.genreNotIn}
            onselect={onGenreSelect}
            onclose={applyFilters}
        />
        <FilterDropdown
            label="Tag"
            icon={Tag}
            items={tags.map((g) => g.value)}
            selectedIn={filters.tagIn}
            selectedNotIn={filters.tagNotIn}
            onselect={onTagSelect}
            onclose={applyFilters}
        />
        <FilterDropdown
            label="Season"
            icon={Calendar}
            items={seasons}
            selectedIn={filters.seasonIn}
            selectedNotIn={filters.seasonNotIn}
            onselect={onSeasonSelect}
            onclose={applyFilters}
        />
        {#if canReadDrafts}
            <ButtonGroup.Root>
                <Button
                    variant={publishFilter === 'published'
                        ? 'default'
                        : 'outline'}
                    size="sm"
                    class="gap-1"
                    onclick={() => {
                        publishFilter =
                            publishFilter === 'published' ? null : 'published';
                        applyFilters();
                    }}
                >
                    <Eye class="size-4" />
                    Published
                </Button>
                <Button
                    variant={publishFilter === 'draft' ? 'default' : 'outline'}
                    size="sm"
                    class="gap-1"
                    onclick={() => {
                        publishFilter =
                            publishFilter === 'draft' ? null : 'draft';
                        applyFilters();
                    }}
                >
                    <EyeOff class="size-4" />
                    Draft
                </Button>
            </ButtonGroup.Root>
        {/if}
    </div>

    <Grid {items} />

    {#if pagination}
        <Pagination links={pagination.links} />
    {/if}
</div>
