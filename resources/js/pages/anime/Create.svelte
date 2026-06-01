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
    import { untrack } from 'svelte';
    import AnimeController from '@/actions/App/Http/Controllers/AnimeController';
    import AppHead from '@/components/app-head.svelte';
    import InputError from '@/components/input-error.svelte';
    import MultiCombobox from '@/components/multi-combobox.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardTitle } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import * as InputGroup from '@/components/ui/input-group/index.js';
    import { Label } from '@/components/ui/label';
    import * as Select from '@/components/ui/select/index.js';
    import { Switch } from '@/components/ui/switch';
    import { Textarea } from '@/components/ui/textarea';
    import { animeApi } from '@/lib/anilist.svelte';
    import { useDisplay } from '@/lib/use-display.svelte';
    import { capitalize } from '@/lib/utils';

    type FormData = App.Data.Anime.AnimeStoreData;

    type Props = App.Data.Anime.AnimeCreateResponse;

    let {
        anime = null,
        genres = [],
        tags = [],
        seasons = [],
        anilistQuery = '',
    }: Props = $props();

    const { mdAndDown } = useDisplay();
    const isMobile = $derived(mdAndDown.current);

    const nullMetadata: App.Data.Anilist.AnilistMediaData = {
        id: null,
        idMal: null,
        coverImage: null,
        title: null,
        startDate: null,
        endDate: null,
        episodes: null,
        description: null,
        bannerImage: null,
        season: null,
        seasonYear: null,
        seasonInt: null,
        genres: [],
        tags: [],
        studios: null,
        characters: null,
    };

    const isEditing = $derived(Boolean(anime));
    const pageTitle = $derived(isEditing ? 'Edit Anime' : 'Create Anime');

    const form = untrack(() => useForm<FormData>({
        title: anime?.title ?? { romaji: '', native: '', en: '', id: '' },
        description: anime?.description ?? { en: '', id: '' },
        anilistId: anime?.anilistId ?? null,
        isPublished: anime?.isPublished ?? false,
        metadata: anime?.metadata ?? { ...nullMetadata, genres: [], tags: [] },
    }));

    let useMalId = $state(false);
    let loading = $state(false);
    let fetchError = $state<string | null>(null);

    async function fetchAnilist() {
        if (!form.anilistId || form.anilistId < 1) {
            return;
        }

        loading = true;
        fetchError = null;
        const result = await animeApi(form.anilistId, useMalId, anilistQuery);

        if (!result) {
            fetchError =
                'Failed to fetch data from AniList. Check the ID and try again.';
            loading = false;

            return;
        }

        form.metadata = result;

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
        loading = false;
    }

    function clearAnilist() {
        form.metadata = { ...nullMetadata, genres: [], tags: [] };
        form.title = { romaji: '', native: '', en: '', id: '' };
        form.description = { en: '', id: '' };
        form.anilistId = null;
        fetchError = null;
    }

    function submit(e: Event) {
        e.preventDefault();
        const id = anime?.id;

        if (isEditing && !id) {
            return;
        }

        if (isEditing) {
            form.put(AnimeController.update(id!).url, { preserveScroll: true });
        } else {
            form.post(AnimeController.store.url());
        }
    }
</script>

<AppHead title={pageTitle} />

{#snippet card(content)}
    {#if isMobile}
        <div class="space-y-4">{@render content()}</div>
    {:else}
        <Card>
            <CardContent class="space-y-4">{@render content()}</CardContent>
        </Card>
    {/if}
{/snippet}

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
        <Label for="anilist_id">Search Source</Label>
        <InputGroup.Root>
            <Select.Root
                type="single"
                value={useMalId ? 'MAL' : 'Anilist'}
                onValueChange={(v) => (useMalId = v === 'MAL')}
            >
                <Select.Trigger class="border-0 shadow-none rounded-none bg-transparent data-[size=default]:h-full w-auto min-w-0 px-2.5 text-foreground data-placeholder:text-foreground">
                    <SelectPrimitive.Value />
                </Select.Trigger>
                <Select.Content>
                    <Select.Item value="Anilist">AniList</Select.Item>
                    <Select.Item value="MAL">MAL</Select.Item>
                </Select.Content>
            </Select.Root>
            <InputGroup.Input
                id="anilist_id"
                type="number"
                min="1"
                bind:value={form.anilistId}
                placeholder={useMalId ? 'MAL ID' : 'AniList ID'}
            />
            <InputGroup.Addon align="inline-end">
                <InputGroup.Button
                    onclick={fetchAnilist}
                    disabled={loading || !form.anilistId}
                    variant="secondary"
                >
                    {loading ? 'Loading...' : 'Autofill'}
                </InputGroup.Button>
                {#if form.metadata?.id}
                    <InputGroup.Button onclick={clearAnilist} variant="ghost">
                        Clear
                    </InputGroup.Button>
                {/if}
            </InputGroup.Addon>
        </InputGroup.Root>
        <InputError message={fetchError ?? undefined} />
        <InputError message={form.errors.anilistId} />
    </div>
    {#if form.metadata?.id}
        {#if form.metadata.coverImage?.extraLarge}
            <img
                src={form.metadata.coverImage.extraLarge}
                alt="Cover"
                class="w-full rounded-lg object-cover"
            />
        {/if}
        <div class="flex flex-wrap gap-2">
            {#if form.metadata.episodes}
                <Badge variant="secondary"
                    >{form.metadata.episodes} episodes
                </Badge>
            {/if}
            {#if form.metadata.studios?.edges?.length}
                {#each form.metadata.studios.edges.slice(0, 3) as edge (edge.node.id)}
                    <Badge variant="secondary">{edge.node.name}</Badge>
                {/each}
            {/if}
        </div>
    {/if}
{/snippet}

{#snippet classificationContent()}
    <CardTitle>Classification</CardTitle>
    <div class="grid gap-2">
        <Label>Genres</Label>
        <MultiCombobox
            label="Select genres"
            items={genres}
            bind:selected={form.metadata!.genres}
            placeholder="Search genres..."
        />
    </div>
    <div class="grid gap-2">
        <Label>Tags</Label>
        <MultiCombobox
            label="Select tags"
            items={tags}
            bind:selected={form.metadata!.tags}
            placeholder="Search tags..."
        />
    </div>
    <div class="grid gap-2">
        <Label>Season & Year</Label>
        <InputGroup.Root>
            <Select.Root type="single" value={form.metadata!.season ?? undefined} onValueChange={(v) => form.metadata!.season = v as App.Enums.Season}>
                <Select.Trigger class="border-0 shadow-none rounded-none bg-transparent data-[size=default]:h-full w-auto min-w-0 px-2.5 text-foreground data-placeholder:text-foreground">
                    <SelectPrimitive.Value placeholder="Season" />
                </Select.Trigger>
                <Select.Content>
                    {#each seasons as s (s)}
                        <Select.Item value={s} label={capitalize(s)}>
                            {capitalize(s)}
                        </Select.Item>
                    {/each}
                </Select.Content>
            </Select.Root>
            <InputGroup.Input
                id="year"
                type="number"
                min="1900"
                max="2100"
                bind:value={form.metadata!.seasonYear}
                placeholder="Year"
            />
        </InputGroup.Root>
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

<div class="p-4 md:mx-auto max-w-5xl">
    <form onsubmit={submit}>
        <div class="grid gap-4 md:gap-6 md:grid-cols-12">
            <div class="space-y-6 md:col-span-8">
                {@render card(basicInfoContent)}
            </div>

            <div
                class="space-y-6 md:col-span-4 md:sticky md:top-4 md:self-start"
            >
                {@render card(metadataContent)}
                {@render card(classificationContent)}
                {@render card(publishingContent)}

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
