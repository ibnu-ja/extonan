<script module lang="ts">
    import { index as animeIndex } from '@/routes/anime';

    export const layout = {
        breadcrumbs: [
            { title: 'Anime', href: animeIndex().url },
            { title: 'Create', href: '' },
        ],
    };
</script>

<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import { Select as SelectPrimitive } from 'bits-ui';
    import AnimeController from '@/actions/App/Http/Controllers/AnimeController';
    import AppHead from '@/components/app-head.svelte';
    import InputError from '@/components/input-error.svelte';
    import MultiCombobox from '@/components/multi-combobox.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardTitle } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import * as Select from '@/components/ui/select/index.js';
    import { Switch } from '@/components/ui/switch';
    import { Textarea } from '@/components/ui/textarea';
    import { animeApi } from '@/lib/anilist.svelte';
    import { useDisplay } from '@/lib/use-display.svelte';

    type MetadataData = App.Data.Anilist.AnilistMediaData;
    type FormData = App.Data.Anime.AnimeStoreData;

    let {
        anime = null,
        genres = [],
        tags = [],
        seasons = [],
        anilistQuery = '',
    }: {
        anime: App.Data.Anime.AnimeFormData | null;
        genres: App.Data.LabelValue[];
        tags: App.Data.LabelValue[];
        seasons: string[];
        anilistQuery?: string;
    } = $props();

    const { mdAndDown } = useDisplay();
    const isMobile = $derived(mdAndDown.current);

    const isEditing = $derived(Boolean(anime));
    const pageTitle = $derived(isEditing ? 'Edit Anime' : 'Create Anime');

    const SEASONS: App.Enums.Season[] = ['WINTER', 'SPRING', 'SUMMER', 'FALL'];

    const availableYears = $derived(
        [
            ...new Set(
                seasons
                    .map((s) => parseInt(s.split(' ').pop() ?? '', 10))
                    .filter((y) => !isNaN(y)),
            ),
        ].sort((a, b) => b - a),
    );

    let anilistData = $state<MetadataData | null>(
        anime?.metadata as MetadataData | null,
    );

    let selectedGenres = $state<string[]>(anilistData?.genres ?? []);
    let selectedTags = $state<string[]>(
        anilistData?.tags?.map((t) => t.name) ?? [],
    );
    let selectedSeasonValue = $state<string | undefined>(
        anilistData?.season?.toUpperCase() ?? undefined,
    );
    let selectedYear = $state<string | undefined>(
        String(anilistData?.seasonYear ?? ''),
    );
    let loading = $state(false);
    let fetchError = $state<string | null>(null);

    const form = useForm<FormData>({
        title: anime?.title ?? { romaji: '', native: '', en: '', id: '' },
        description: anime?.description ?? { en: '', id: '' },
        anilistId: anime?.anilistId ?? null,
        isPublished: anime?.isPublished ?? false,
        metadata: anime?.metadata as MetadataData | null,
    });

    function buildMetadata(): MetadataData | null {
        if (!anilistData) {
            return null;
        }

        const data = anilistData;

        return {
            ...data,
            genres: [...selectedGenres],
            tags: selectedTags.map((name) => {
                const existing = data.tags?.find((t) => t.name === name);

                return (
                    existing ?? {
                        id: 0,
                        name,
                        rank: 0,
                        isAdult: false,
                        category: '',
                        isMediaSpoiler: false,
                        isGeneralSpoiler: false,
                    }
                );
            }),
            ...(selectedSeasonValue && selectedYear
                ? {
                      season: selectedSeasonValue,
                      seasonYear: parseInt(selectedYear, 10),
                  }
                : {}),
        };
    }

    async function fetchAnilist() {
        if (!form.anilistId || form.anilistId < 1) {
            return;
        }

        loading = true;
        fetchError = null;
        const result = await animeApi(form.anilistId, false, anilistQuery);

        if (!result) {
            fetchError =
                'Failed to fetch data from AniList. Check the ID and try again.';
            loading = false;

            return;
        }

        anilistData = result;

        form.title = {
            romaji: result.title?.romaji ?? form.title.romaji ?? '',
            native: result.title?.native ?? form.title.native ?? '',
            en: result.title?.english ?? form.title.en ?? '',
        };
        form.description = {
            ...form.description,
            en: result.description ?? form.description.en ?? '',
        };
        form.anilistId = result.id;

        selectedGenres = [...(result.genres ?? [])];
        selectedTags = (result.tags ?? []).map((t) => t.name);
        selectedSeasonValue = result.season?.toUpperCase() ?? undefined;
        selectedYear = String(result.seasonYear ?? '');
        loading = false;
    }

    function clearAnilist() {
        anilistData = null;
        form.title = { romaji: '', native: '', en: '', id: '' };
        form.description = { en: '', id: '' };
        form.anilistId = null;
        selectedGenres = [];
        selectedTags = [];
        selectedSeasonValue = undefined;
        selectedYear = undefined;
        fetchError = null;
    }

    async function submit(e: Event) {
        e.preventDefault();
        const id = anime?.id;

        if (isEditing && !id) {
            return;
        }

        form.transform((data) => ({
            ...data,
            metadata: buildMetadata(),
        }));

        const options = { preserveScroll: true };

        if (isEditing) {
            form.put(AnimeController.update(id!).url, options);
        } else {
            form.post(AnimeController.store.url(), options);
        }
    }
</script>

<AppHead title={pageTitle} />

{#snippet basicInfoContent()}
    <CardTitle>Basic Information</CardTitle>
    <div class="grid gap-2">
        <Label for="title_romaji">Title (Romaji)</Label>
        <Input
            id="title_romaji"
            bind:value={form.title.romaji}
            required
            placeholder="Title in romaji"
        />
        <InputError message={form.errors['title.romaji']} />
    </div>
    <div class="grid gap-2">
        <Label for="title_native">Title (Native)</Label>
        <Input
            id="title_native"
            bind:value={form.title.native}
            required
            placeholder="Title in native script"
        />
        <InputError message={form.errors['title.native']} />
    </div>
    <div class="grid gap-2">
        <Label for="title_en">Title (English)</Label>
        <Input
            id="title_en"
            bind:value={form.title.en}
            placeholder="English title"
        />
        <InputError message={form.errors['title.en']} />
    </div>
    <div class="grid gap-2">
        <Label for="title_id">Title (Indonesian)</Label>
        <Input
            id="title_id"
            bind:value={form.title.id}
            placeholder="Indonesian title"
        />
        <InputError message={form.errors['title.id']} />
    </div>
    <div class="grid gap-2">
        <Label for="description">Description</Label>
        <Textarea
            id="description"
            bind:value={form.description.en}
            rows={6}
            placeholder="Anime description"
        />
        <InputError message={form.errors['description.en']} />
    </div>
{/snippet}

{#snippet metadataContent()}
    <CardTitle>AniList Metadata</CardTitle>
    <div class="grid gap-2">
        <Label for="anilist_id">AniList ID / MAL ID</Label>
        <div class="flex gap-2">
            <div class="flex-1">
                <Input
                    id="anilist_id"
                    type="number"
                    min="1"
                    bind:value={form.anilistId}
                    placeholder="Enter AniList or MAL ID"
                />
            </div>
            <Button
                type="button"
                onclick={fetchAnilist}
                disabled={loading || !form.anilistId}
            >
                {loading ? 'Loading...' : 'Autofill'}
            </Button>
            {#if anilistData}
                <Button type="button" variant="outline" onclick={clearAnilist}
                    >Clear</Button
                >
            {/if}
        </div>
        <InputError message={fetchError ?? undefined} />
        <InputError message={form.errors.anilistId} />
    </div>
    {#if anilistData}
        {#if anilistData.coverImage?.extraLarge}
            <img
                src={anilistData.coverImage.extraLarge}
                alt="Cover"
                class="w-full rounded-lg object-cover"
            />
        {/if}
        <div class="flex flex-wrap gap-2">
            {#if anilistData.episodes}
                <Badge variant="secondary"
                    >{anilistData.episodes} episodes</Badge
                >
            {/if}
            {#if anilistData.studios?.edges?.length}
                {#each anilistData.studios.edges.slice(0, 3) as edge (edge.node.id)}
                    <Badge variant="secondary">{edge.node.name}</Badge>
                {/each}
            {/if}
        </div>
    {/if}
{/snippet}

{#snippet classificationContent()}
    <CardTitle>Classification Overrides</CardTitle>
    <div class="grid gap-2">
        <Label>Genres</Label>
        <MultiCombobox
            label="Select genres"
            items={genres}
            bind:selected={selectedGenres}
            placeholder="Search genres..."
        />
    </div>
    <div class="grid gap-2">
        <Label>Tags</Label>
        <MultiCombobox
            label="Select tags"
            items={tags}
            bind:selected={selectedTags}
            placeholder="Search tags..."
        />
    </div>
    <div class="flex gap-4">
        <div class="flex-1 grid gap-2">
            <Label>Season</Label>
            <Select.Root type="single" bind:value={selectedSeasonValue}>
                <Select.Trigger class="w-full"
                    ><SelectPrimitive.Value
                        placeholder="Season"
                    /></Select.Trigger
                >
                <Select.Content>
                    {#each SEASONS as s (s)}
                        <Select.Item value={s} label={s}>{s}</Select.Item>
                    {/each}
                </Select.Content>
            </Select.Root>
        </div>
        <div class="flex-1 grid gap-2">
            <Label>Year</Label>
            <Select.Root type="single" bind:value={selectedYear}>
                <Select.Trigger class="w-full"
                    ><SelectPrimitive.Value
                        placeholder="Year"
                    /></Select.Trigger
                >
                <Select.Content class="max-h-72">
                    {#each availableYears as y (y)}
                        <Select.Item value={String(y)} label={String(y)}
                            >{y}</Select.Item
                        >
                    {/each}
                </Select.Content>
            </Select.Root>
        </div>
    </div>
{/snippet}

{#snippet publishingContent()}
    <CardTitle>Publishing</CardTitle>
    <div class="flex items-center gap-2">
        <Switch
            checked={form.isPublished}
            onCheckedChange={(v) => (form.isPublished = v)}
            id="is_published"
        />
        <Label for="is_published">Published</Label>
    </div>
    <InputError message={form.errors.isPublished} />
{/snippet}

<div class="p-4 mx-auto max-w-5xl">
    <form onsubmit={submit}>
        <div class="grid gap-4 md:gap-6 md:grid-cols-12">
            <div class="space-y-6 md:col-span-8">
                {#if isMobile}
                    <div class="space-y-4">
                        {@render basicInfoContent()}
                    </div>
                {:else}
                    <Card>
                        <CardContent class="space-y-4">
                            {@render basicInfoContent()}
                        </CardContent>
                    </Card>
                {/if}
            </div>

            <div
                class="space-y-6 md:col-span-4 md:sticky md:top-4 md:self-start"
            >
                {#if isMobile}
                    <div class="space-y-4">
                        {@render metadataContent()}
                    </div>
                {:else}
                    <Card>
                        <CardContent class="space-y-4">
                            {@render metadataContent()}
                        </CardContent>
                    </Card>
                {/if}

                {#if isMobile}
                    <div class="space-y-4">
                        {@render classificationContent()}
                    </div>
                {:else}
                    <Card>
                        <CardContent class="space-y-4">
                            {@render classificationContent()}
                        </CardContent>
                    </Card>
                {/if}

                {#if isMobile}
                    <div class="space-y-4">
                        {@render publishingContent()}
                    </div>
                {:else}
                    <Card>
                        <CardContent class="space-y-4">
                            {@render publishingContent()}
                        </CardContent>
                    </Card>
                {/if}

                <Button type="submit" disabled={form.processing} class="w-full">
                    {form.processing
                        ? 'Saving...'
                        : isEditing
                          ? 'Update'
                          : 'Save'}
                </Button>
            </div>
        </div>
    </form>
</div>
