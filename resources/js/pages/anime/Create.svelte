<script lang="ts">
    import { router, setLayoutProps, useForm } from '@inertiajs/svelte';
    import { Check, ChevronDown, Send, Trash2, X } from 'lucide-svelte';
    import type { Snippet } from 'svelte';
    import { untrack } from 'svelte';
    import AnimeController from '@/actions/App/Http/Controllers/AnimeController';
    import Casts from '@/components/anime/casts.svelte';
    import AppHead from '@/components/app-head.svelte';
    import MultiCombobox from '@/components/multi-combobox.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import * as Card from '@/components/ui/card/index.js';
    import * as DropdownMenu from '@/components/ui/dropdown-menu/index.js';
    import * as Field from '@/components/ui/field/index';
    import { Input } from '@/components/ui/input';
    import * as InputGroup from '@/components/ui/input-group/index.js';
    import { Switch } from '@/components/ui/switch';
    import { animeApi } from '@/lib/anilist.svelte';
    import { t } from '@/lib/locale.svelte';
    import { useDisplay } from '@/lib/use-display.svelte';
    import { capitalize } from '@/lib/utils';
    import { index as animeIndex, show as animeShow } from '@/routes/anime';

    type FormData = App.Data.Anime.AnimeStoreData;

    type Props = App.Data.Anime.AnimeCreateResponse;

    let {
        anime,
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
    const pageTitle = $derived(
        isEditing && anime ? `Editing ${t(anime.title)}` : 'Create Anime',
    );

    $effect(() => {
        if (isEditing && anime) {
            setLayoutProps({
                title: `Editing ${t(anime.title)}`,
                breadcrumbs: [
                    { title: 'Anime', href: animeIndex().url },
                    { title: t(anime.title), href: animeShow.url(anime.id!) },
                    { title: 'Edit', href: '' },
                ],
            });
        } else {
            setLayoutProps({
                title: 'Create Anime',
                breadcrumbs: [
                    { title: 'Anime', href: animeIndex().url },
                    { title: 'Create', href: '' },
                ],
            });
        }
    });

    const form = untrack(() =>
        useForm<FormData>({
            title: anime?.title ?? { romaji: '', native: '', en: '', id: '' },
            description: anime?.description ?? { en: '', id: '' },
            anilistId: anime?.anilistId ?? null,
            isPublished: anime?.isPublished ?? false,
            metadata: anime?.metadata ?? {
                ...nullMetadata,
                genres: [],
                tags: [],
            },
        }),
    );

    let useMalId = $state(false);
    let loading = $state(false);
    let fetchError = $state<string | null>(null);

    const languages: { label: string; value: 'en' | 'id' }[] = [
        { label: 'English', value: 'en' },
        { label: 'Indonesian', value: 'id' },
    ];
    let currentLang = $state<'en' | 'id'>('en');

    async function fetchAnilist() {
        if (loading || !form.anilistId || form.anilistId < 1) {
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
        form.reset('metadata', 'title', 'description', 'anilistId');
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

    function deleteAnime() {
        if (!anime?.id) {
            return;
        }

        if (confirm('Are you sure you want to delete this anime?')) {
            router.delete(AnimeController.destroy(anime.id).url);
        }
    }
</script>

<AppHead title={pageTitle} />

{#snippet sectionHeader(title: string, action?: Snippet)}
    <Card.Header class="px-0 md:px-6">
        <Card.Title>{title}</Card.Title>
        {#if action}<Card.Action>{@render action()}</Card.Action>{/if}
    </Card.Header>
{/snippet}

{#snippet card(title: string, content: Snippet, action?: Snippet)}
    {#if isMobile}
        <div class="space-y-3">
            {@render sectionHeader(title, action)}
            {@render content()}
        </div>
    {:else}
        <Card.Root>
            {@render sectionHeader(title, action)}
            <Card.Content class="space-y-3">{@render content()}</Card.Content>
        </Card.Root>
    {/if}
{/snippet}

{#snippet basicInfoContent()}
    <Field.Field>
        <Field.Label for="title_lang">Title</Field.Label>
        <InputGroup.Root>
            <InputGroup.Addon>
                <DropdownMenu.Root>
                    <DropdownMenu.Trigger>
                        {#snippet child({ props: triggerProps })}
                            <InputGroup.Button variant="default" {...triggerProps}>
                                {languages.find((l) => l.value === currentLang)?.label ?? 'Language'}
                                <ChevronDown class="ml-1 size-4 opacity-50" />
                            </InputGroup.Button>
                        {/snippet}
                    </DropdownMenu.Trigger>
                    <DropdownMenu.Content>
                        {#each languages as lang (lang.value)}
                            <DropdownMenu.Item onclick={() => (currentLang = lang.value)}>
                                <div class="flex w-full items-center gap-2">
                                    {#if currentLang === lang.value}
                                        <Check class="size-4" />
                                    {/if}
                                    <span>{lang.label}</span>
                                </div>
                            </DropdownMenu.Item>
                        {/each}
                    </DropdownMenu.Content>
                </DropdownMenu.Root>
            </InputGroup.Addon>
            <InputGroup.Input
                id="title_lang"
                bind:value={form.title[currentLang]}
                placeholder={`Title in ${languages.find((l) => l.value === currentLang)?.label}`}
            />
        </InputGroup.Root>
        <Field.Error>{form.errors[`title.${currentLang}`]}</Field.Error>
    </Field.Field>
    <Field.Field>
        <Field.Label for="title_romaji">Title (Romaji)</Field.Label>
        <Input
            id="title_romaji"
            bind:value={form.title.romaji}
            required
            placeholder="Title in romaji"
        />
        <Field.Error>{form.errors['title.romaji']}</Field.Error>
    </Field.Field>
    <Field.Field>
        <Field.Label for="title_native">Title (Native)</Field.Label>
        <Input
            id="title_native"
            bind:value={form.title.native}
            required
            placeholder="Title in native script"
        />
        <Field.Error>{form.errors['title.native']}</Field.Error>
    </Field.Field>
    <Field.Field>
        <Field.Label for="description_lang">Description</Field.Label>
        <InputGroup.Root>
            <InputGroup.Textarea
                id="description_lang"
                bind:value={form.description[currentLang]}
                placeholder={`Description in ${languages.find((l) => l.value === currentLang)?.label}`}
                rows={9}
            />
            <InputGroup.Addon align="block-start" class="border-b">
                <DropdownMenu.Root>
                    <DropdownMenu.Trigger>
                        {#snippet child({ props: triggerProps })}
                            <InputGroup.Button variant="default" {...triggerProps}>
                                {languages.find((l) => l.value === currentLang)?.label ?? 'Language'}
                                <ChevronDown class="ml-1 size-4 opacity-50" />
                            </InputGroup.Button>
                        {/snippet}
                    </DropdownMenu.Trigger>
                    <DropdownMenu.Content>
                        {#each languages as lang (lang.value)}
                            <DropdownMenu.Item onclick={() => (currentLang = lang.value)}>
                                <div class="flex w-full items-center gap-2">
                                    {#if currentLang === lang.value}
                                        <Check class="size-4" />
                                    {/if}
                                    <span>{lang.label}</span>
                                </div>
                            </DropdownMenu.Item>
                        {/each}
                    </DropdownMenu.Content>
                </DropdownMenu.Root>
            </InputGroup.Addon>
        </InputGroup.Root>
        <Field.Error>{form.errors[`description.${currentLang}`]}</Field.Error>
    </Field.Field>
{/snippet}

{#snippet metadataContent()}
    <Field.Field>
        <Field.Label for="anilist_id">Search Source</Field.Label>
        <InputGroup.Root>
            <InputGroup.Addon>
                <DropdownMenu.Root>
                    <DropdownMenu.Trigger>
                        {#snippet child({ props: triggerProps })}
                            <InputGroup.Button variant="default" {...triggerProps}>
                                {useMalId ? 'MAL' : 'AniList'}
                            </InputGroup.Button>
                        {/snippet}
                    </DropdownMenu.Trigger>
                    <DropdownMenu.Content>
                        <DropdownMenu.Item onclick={() => (useMalId = false)}>
                            <div class="flex w-full items-center gap-2">
                                {#if !useMalId}
                                    <Check class="size-4" />
                                {/if}
                                <span>AniList</span>
                            </div>
                        </DropdownMenu.Item>
                        <DropdownMenu.Item onclick={() => (useMalId = true)}>
                            <div class="flex w-full items-center gap-2">
                                {#if useMalId}
                                    <Check class="size-4" />
                                {/if}
                                <span>MAL</span>
                            </div>
                        </DropdownMenu.Item>
                    </DropdownMenu.Content>
                </DropdownMenu.Root>
            </InputGroup.Addon>
            <InputGroup.Input
                id="anilist_id"
                type="number"
                min="1"
                bind:value={form.anilistId}
                placeholder={useMalId ? 'MAL ID' : 'AniList ID'}
                onkeydown={(e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        fetchAnilist();
                    }
                }}
            />
            <InputGroup.Addon align="inline-end">
                <InputGroup.Button
                    onclick={fetchAnilist}
                    aria-disabled={loading || !form.anilistId}
                    variant="secondary"
                    size="icon-xs"
                    aria-label="Autofill"
                >
                    <Send class="size-4" />
                </InputGroup.Button>
                {#if form.metadata?.id}
                    <InputGroup.Button
                        onclick={clearAnilist}
                        variant="ghost"
                        size="icon-xs"
                        aria-label="Clear"
                    >
                        <X class="size-4" />
                    </InputGroup.Button>
                {/if}
            </InputGroup.Addon>
        </InputGroup.Root>
        <Field.Error>{fetchError}</Field.Error>
        <Field.Error>{form.errors.anilistId}</Field.Error>
    </Field.Field>
    {#if form.metadata?.id}
        {#if form.metadata.coverImage?.extraLarge}
            <img
                src={form.metadata.coverImage.extraLarge}
                alt="Cover"
                class="w-full rounded-lg object-cover"
            />
        {/if}
        <div class="flex flex-wrap gap-2">
            {#if form.metadata.startDate?.year}
                <Badge variant="secondary">
                    {new Date(
                        form.metadata.startDate.year,
                        (form.metadata.startDate.month ?? 1) - 1,
                        form.metadata.startDate.day ?? 1,
                    ).toLocaleDateString('en-US', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric',
                    })}
                </Badge>
            {/if}
            {#if form.metadata.episodes}
                <Badge variant="secondary">{form.metadata.episodes} episodes</Badge>
            {/if}
            {#if form.metadata.studios?.edges?.length}
                {#each form.metadata.studios.edges.slice(0, 3) as edge (edge.node?.id)}
                    {#if edge.node}
                        <Badge variant="secondary">{edge.node.name}</Badge>
                    {/if}
                {/each}
            {/if}
        </div>
    {/if}
{/snippet}

{#snippet classificationContent()}
    <Field.Field>
        <Field.Label>Genres</Field.Label>
        <MultiCombobox
            label="Select genres"
            items={genres}
            bind:selected={form.metadata!.genres}
            placeholder="Search genres..."
        />
    </Field.Field>
    <Field.Field>
        <Field.Label>Tags</Field.Label>
        <MultiCombobox
            label="Select tags"
            items={tags}
            bind:selected={form.metadata!.tags}
            placeholder="Search tags..."
        />
    </Field.Field>
    <Field.Field>
        <Field.Label>Season & Year</Field.Label>
        <InputGroup.Root>
            <InputGroup.Addon>
                <DropdownMenu.Root>
                    <DropdownMenu.Trigger>
                        {#snippet child({ props: triggerProps })}
                            <InputGroup.Button variant="default" {...triggerProps}>
                                {form.metadata!.season
                                    ? capitalize(form.metadata!.season)
                                    : 'Season'}
                                <ChevronDown class="ml-1 size-4 opacity-50" />
                            </InputGroup.Button>
                        {/snippet}
                    </DropdownMenu.Trigger>
                    <DropdownMenu.Content>
                        {#each seasons as s (s)}
                            <DropdownMenu.Item onclick={() => (form.metadata!.season = s)}>
                                <div class="flex w-full items-center gap-2">
                                    {#if form.metadata!.season === s}
                                        <Check class="size-4" />
                                    {/if}
                                    <span>{capitalize(s)}</span>
                                </div>
                            </DropdownMenu.Item>
                        {/each}
                    </DropdownMenu.Content>
                </DropdownMenu.Root>
            </InputGroup.Addon>
            <InputGroup.Input
                id="year"
                type="number"
                min="1900"
                max="2100"
                bind:value={form.metadata!.seasonYear}
                placeholder="Year"
            />
        </InputGroup.Root>
    </Field.Field>
{/snippet}

{#snippet publishingContent()}
    <Field.Field orientation="horizontal">
        <Field.Content>
            <Field.Label for="is_published">Published</Field.Label>
        </Field.Content>
        <Switch
            checked={form.isPublished}
            onCheckedChange={(v) => (form.isPublished = v)}
            id="is_published"
        />
        <Field.Error slot="error">{form.errors.isPublished}</Field.Error>
    </Field.Field>
{/snippet}

{#snippet castsContent()}
    <Casts characters={form.metadata!.characters!.edges} />
{/snippet}

<div class="p-4 w-full mx-auto max-w-400">
    <form onsubmit={submit}>
        <div class="grid gap-4 md:gap-6 md:grid-cols-12">
            <div class="space-y-6 md:col-span-8">
                {@render card('Basic Information', basicInfoContent)}

                {#if form.metadata?.characters?.edges?.length}
                    {@render card('Casts', castsContent)}
                {/if}
            </div>

            <div class="space-y-6 md:col-span-4 md:sticky md:top-4 md:self-start">
                {@render card('Autofill', metadataContent)}
                {@render card('Classification', classificationContent)}
                {@render card('Publishing', publishingContent)}

                <div class="flex gap-2">
                    {#if isEditing}
                        <Button
                            type="button"
                            variant="destructive"
                            size="icon"
                            onclick={deleteAnime}
                            disabled={form.processing}
                        >
                            <Trash2 class="size-4" />
                        </Button>
                    {/if}
                    {#if form.isDirty}
                        <Button
                            type="button"
                            variant="outline"
                            onclick={() => {
                                form.reset();
                                fetchError = null;
                            }}
                            disabled={form.processing}
                        >
                            Reset
                        </Button>
                    {/if}
                    <Button
                        type="submit"
                        disabled={form.processing}
                        class="flex-1"
                    >
                        {form.processing
                            ? 'Saving...'
                            : isEditing
                              ? 'Update'
                              : 'Save'}
                    </Button>
                </div>
            </div>
        </div>
    </form>
</div>
