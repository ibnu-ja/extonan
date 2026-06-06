<script module lang="ts">
    import { create as animeCreate, index as animeIndex } from '@/routes/anime';

    export const layout = {
        breadcrumbs: [{ title: 'Anime', href: animeIndex() }],
    };
</script>

<script lang="ts">
    import { Link, page, router } from '@inertiajs/svelte';
    import {
        Clapperboard,
        Plus,
        Tag,
        Calendar,
        Eye,
        EyeOff,
    } from 'lucide-svelte';
    import Pagination from '@/components/pagination.svelte';
    import { Button } from '@/components/ui/button';
    import * as ButtonGroup from '@/components/ui/button-group';
    import { useAuth } from '@/lib/auth.svelte';
    import { useAnime } from '@/lib/use-anime.svelte';
    import type { AnimeFilterState } from '@/lib/use-anime.svelte';
    import type { RouteQueryOptions } from '@/wayfinder';
    import Fab from './index/components/fab.svelte';
    import FilterBar from './index/components/filter-bar.svelte';
    import FilterDropdown from './index/components/filter-dropdown.svelte';
    import Grid from './index/components/grid.svelte';

    let {
        anime,
        sortOptions = [],
        perPageValues = [14, 25, 50, 100],
    }: App.Data.Anime.AnimeIndexResponse = $props();

    // TODO: Show validation errors from page.props.errors

    const { genres, tags, seasons, buildFilterQuery } = useAnime();
    const { can } = useAuth();

    let debounceTimer: ReturnType<typeof setTimeout> | undefined;

    function debouncedApply() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(applyFilters, 400);
    }

    function pageSearchParams(): URLSearchParams {
        const idx = page.url.indexOf('?');

        return idx >= 0
            ? new URLSearchParams(page.url.slice(idx))
            : new URLSearchParams();
    }

    function parseFilterState(): AnimeFilterState {
        const params = pageSearchParams();
        const state: AnimeFilterState = {
            title: '',
            genreIn: [],
            genreNotIn: [],
            tagIn: [],
            tagNotIn: [],
            seasonIn: [],
            seasonNotIn: [],
            isPublished: null,
        };

        for (const [key, value] of params) {
            const match = key.match(/^filter\[(\w+)](?:\[\d*])?$/);

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
    let sort = $state(pageSearchParams().get('sort') || 'title->romaji');
    let selectedTagNames = $derived(
        filters.tagIn.map((id) => tags.find((t) => t.key === id)?.value ?? id),
    );
    let selectedTagNotInNames = $derived(
        filters.tagNotIn.map(
            (id) => tags.find((t) => t.key === id)?.value ?? id,
        ),
    );
    let publishFilter = $state<'published' | 'draft' | null>(
        (() => {
            const val = pageSearchParams().get('filter[is_published]');

            return val === 'true'
                ? 'published'
                : val === 'false'
                  ? 'draft'
                  : null;
        })(),
    );

    function applyFilters() {
        const query = buildFilterQuery({
            filter: {
                ...filters,
                isPublished:
                    publishFilter === 'published'
                        ? true
                        : publishFilter === 'draft'
                          ? false
                          : null,
            },
            sort: sort || null,
            perPage: null,
        } satisfies App.Data.Anime.AnimeIndexRequest);

        router.get(animeIndex.url({ query } as RouteQueryOptions), undefined, {
            preserveState: true,
            preserveScroll: true,
            only: ['anime'],
        });
    }

    function onSearchChange(value: string) {
        filters.title = value;
        debouncedApply();
    }

    function onGenreSelect(item: string, mode: 'in' | 'notIn' | 'none') {
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

        debouncedApply();
    }

    function onTagSelect(item: string, mode: 'in' | 'notIn' | 'none') {
        const tagId = tags.find((t) => t.value === item)?.key ?? item;

        if (mode === 'in') {
            filters.tagIn = [...filters.tagIn, tagId];
            filters.tagNotIn = filters.tagNotIn.filter((t) => t !== tagId);
        } else if (mode === 'notIn') {
            filters.tagNotIn = [...filters.tagNotIn, tagId];
            filters.tagIn = filters.tagIn.filter((t) => t !== tagId);
        } else {
            filters.tagIn = filters.tagIn.filter((t) => t !== tagId);
            filters.tagNotIn = filters.tagNotIn.filter((t) => t !== tagId);
        }

        debouncedApply();
    }

    function onSeasonSelect(item: string, mode: 'in' | 'notIn' | 'none') {
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

        debouncedApply();
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
        />
        <FilterDropdown
            label="Tag"
            icon={Tag}
            items={tags.map((g) => g.value)}
            selectedIn={selectedTagNames}
            selectedNotIn={selectedTagNotInNames}
            onselect={onTagSelect}
        />
        <FilterDropdown
            label="Season"
            icon={Calendar}
            items={seasons}
            selectedIn={filters.seasonIn}
            selectedNotIn={filters.seasonNotIn}
            onselect={onSeasonSelect}
        />
        {#if can('post.read.self')}
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

    <Grid items={anime.data} />

    {#if anime}
        <Pagination data={anime} only={['anime']} {perPageValues} />
    {/if}

    {#if can('post.create')}
        <Fab>
            {#snippet child({ props })}
                <Link href={animeCreate().url} prefetch="hover" {...props}>
                    <Plus class="size-6" />
                    Add
                </Link>
            {/snippet}
        </Fab>
    {/if}
</div>
