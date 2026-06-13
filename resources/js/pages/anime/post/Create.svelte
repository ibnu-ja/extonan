<script lang="ts">
    import { Link, router, setLayoutProps, useForm } from '@inertiajs/svelte';
    import Check from 'lucide-svelte/icons/check';
    import ChevronDown from 'lucide-svelte/icons/chevron-down';
    import ChevronUp from 'lucide-svelte/icons/chevron-up';
    import ExternalLink from 'lucide-svelte/icons/external-link';
    import Plus from 'lucide-svelte/icons/plus';
    import Trash2 from 'lucide-svelte/icons/trash-2';
    import { untrack } from 'svelte';
    import type { Snippet } from 'svelte';
    import PostController from '@/actions/App/Http/Controllers/PostController';
    import AppHead from '@/components/app-head.svelte';
    import MediaManager from '@/components/media-manager/index.svelte';
    import MediaManagerTrigger from '@/components/media-manager/media-manager-trigger.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import * as Card from '@/components/ui/card/index.js';
    import * as DropdownMenu from '@/components/ui/dropdown-menu/index.js';
    import * as Field from '@/components/ui/field/index.js';
    import { Input } from '@/components/ui/input';
    import * as InputGroup from '@/components/ui/input-group/index.js';
    import { Switch } from '@/components/ui/switch';
    import { Textarea } from '@/components/ui/textarea';
    import { t } from '@/lib/locale.svelte';
    import { useDisplay } from '@/lib/use-display.svelte';
    import { index as animeIndex, show as animeShow } from '@/routes/anime';
    import { show as postShow } from '@/routes/post';

    type FormData = App.Data.Anime.PostStoreData;
    type Props = App.Data.Anime.PostCreateResponse;

    let { anime, post, canPublish = false, metadata = null }: Props = $props();

    const { mdAndDown } = useDisplay();
    const isMobile = $derived(mdAndDown.current);

    const pageTitle = $derived(
        post ? `Editing ${t(post.title)}` : 'Create Episode',
    );

    let currentLang = $state<'en' | 'id'>('en');
    let showAnimeImage = $state(false);

    const languages: { label: string; value: 'en' | 'id' }[] = [
        { label: 'English', value: 'en' },
        { label: 'Indonesian', value: 'id' },
    ];

    const postTypes = [
        { value: 'tv', label: 'TV Ep' },
        { value: 'bd', label: 'Blu-ray BOX/DISC' },
        { value: 'movie', label: 'Movie' },
    ];

    const streamTypes = [
        { value: 'saluran', label: 'Stream (M3U8)' },
        { value: 'embed', label: 'Embed' },
    ] as const;

    const form = untrack(() =>
        useForm<FormData>({
            title: post?.title ?? { en: '', id: '', romaji: '', native: '' },
            description: post?.description ?? { en: '', id: '' },
            postType: post?.postType ?? 'tv',
            epNo: post?.epNo ?? '',
            isPublished: post?.isPublished ?? false,
            links: post?.links ?? [],
            embed: post?.embed ?? null,
            saluran: post?.saluran ?? null,
            thumbnailItem: post?.thumbnailItem ?? null,
        }),
    );

    const hasEmbed = $derived(Boolean(form.embed));
    const hasSaluran = $derived(Boolean(form.saluran));

    const availableStreamTypes = $derived(
        streamTypes.filter((st) => {
            if (st.value === 'embed' && hasEmbed) {
                return false;
            }

            if (st.value === 'saluran' && hasSaluran) {
                return false;
            }

            return true;
        }),
    );

    function addStream(type: App.Enums.ResourceType) {
        if (type === 'embed') {
            form.embed = {
                name: 'embed',
                type: 'embed',
                value: [{ name: '', value: '' }],
            };
        } else {
            form.saluran = {
                name: 'saluran',
                type: 'saluran',
                value: [{ name: '', value: '' }],
            };
        }
    }

    function clearEmbed() {
        form.embed = null;
    }
    function clearSaluran() {
        form.saluran = null;
    }

    function addStreamMirror() {
        if (!form.saluran) {
            return;
        }

        form.saluran.value = [...form.saluran.value, { name: '', value: '' }];
    }

    function deleteStreamMirror(j: number) {
        if (!form.saluran) {
            return;
        }

        form.saluran.value = form.saluran.value.filter((_, k) => k !== j);
    }

    function addEmbedMirror() {
        if (!form.embed) {
            return;
        }

        form.embed.value = [...form.embed.value, { name: '', value: '' }];
    }

    function deleteEmbedMirror(j: number) {
        if (!form.embed) {
            return;
        }

        form.embed.value = form.embed.value.filter((_, k) => k !== j);
    }

    $effect(() => {
        if (post) {
            setLayoutProps({
                title: `Editing ${t(post.title)}`,
                breadcrumbs: [
                    { title: 'Anime', href: animeIndex().url },
                    { title: t(anime.title), href: animeShow.url(anime.id) },
                    {
                        title: t(post.title),
                        href: postShow.url({ anime: anime.id, post: post.id! }),
                    },
                    { title: 'Edit', href: '' },
                ],
            });
        } else {
            setLayoutProps({
                title: 'Create Episode',
                breadcrumbs: [
                    { title: 'Anime', href: animeIndex().url },
                    { title: t(anime.title), href: animeShow.url(anime.id) },
                    { title: 'Create Episode', href: '' },
                ],
            });
        }
    });

    function submit(e: Event) {
        e.preventDefault();

        if (post) {
            form.put(
                PostController.update({ anime: anime.id, post: post.id! }).url,
                { preserveScroll: true },
            );
        } else {
            form.post(PostController.store(anime.id).url);
        }
    }

    function publish() {
        form.isPublished = true;
        submit(new Event('submit'));
    }

    function deletePost() {
        if (!post) {
            return;
        }

        if (confirm('Are you sure you want to delete this episode?')) {
            router.delete(
                PostController.destroy({ anime: anime.id, post: post.id! }).url,
            );
        }
    }

    function addLink() {
        form.links = [
            ...form.links,
            { name: '', type: 'link', value: [{ name: '', value: '' }] },
        ];
    }

    function deleteLink(index: number) {
        form.links = form.links.filter((_, i) => i !== index);
    }

    function addMirror(index: number) {
        form.links[index].value = [
            ...form.links[index].value,
            { name: '', value: '' },
        ];
    }

    function deleteMirror(i: number, j: number) {
        form.links[i].value = form.links[i].value.filter((_, k) => k !== j);
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
            <Card.Content>{@render content()}</Card.Content>
        </Card.Root>
    {/if}
{/snippet}

{#snippet mirrorFields(
    resource: App.Data.Anime.ResourceData,
    deleteFn: (i: number) => void,
    _,
    placeholderName?: string,
    placeholderValue?: string,
)}
    {#each resource.value as _, j (j)}
        <div class="flex items-start gap-2">
            <div class="flex-1 space-y-3">
                <Field.Field>
                    <Field.Label>{placeholderName ?? 'Mirror name'}</Field.Label
                    >
                    <Input
                        bind:value={resource.value[j].name}
                        placeholder={placeholderName ?? 'Mirror name'}
                    />
                </Field.Field>
                <Field.Field>
                    <Field.Label
                        >{placeholderValue?.startsWith('<')
                            ? 'Embed Code'
                            : 'URL'}</Field.Label
                    >
                    <Textarea
                        bind:value={resource.value[j].value}
                        placeholder={placeholderValue ?? 'https://...'}
                    />
                </Field.Field>
            </div>
            <Button
                variant="ghost"
                size="icon"
                class="text-destructive shrink-0 mt-7"
                onclick={() => deleteFn(j)}
            >
                <Trash2 class="size-4" />
            </Button>
        </div>
    {/each}
{/snippet}

{#snippet resourceHeader(title: string, deleteFn: () => void)}
    <Card.Header class="px-0 md:px-6">
        <Card.Title>{title}</Card.Title>
        <Card.Action>
            <Button
                variant="ghost"
                size="icon"
                class="text-destructive shrink-0"
                onclick={deleteFn}
            >
                <Trash2 class="size-4" />
            </Button>
        </Card.Action>
    </Card.Header>
{/snippet}

{#snippet streamCard(
    title: string,
    resource: App.Data.Anime.ResourceData | null,
    clearFn: () => void,
    addFn: () => void,
    deleteFn: (i: number) => void,
    placeholderValue: string,
)}
    {#if resource}
        {#if isMobile}
            <div class="space-y-3">
                {@render resourceHeader(title, clearFn)}
                {#if resource.value.length === 0}
                    <p>No mirrors.</p>
                {:else}
                    {@render mirrorFields(
                        resource,
                        deleteFn,
                        0,
                        'Mirror name',
                        placeholderValue,
                    )}
                {/if}
                <Button variant="outline" size="sm" onclick={addFn}
                    ><Plus class="mr-1 size-4" /> Add Mirror</Button
                >
            </div>
        {:else}
            <Card.Root>
                {@render resourceHeader(title, clearFn)}
                <Card.Content class="space-y-3">
                    {#if resource.value.length === 0}
                        <p>No mirrors.</p>
                    {:else}
                        {@render mirrorFields(
                            resource,
                            deleteFn,
                            0,
                            'Mirror name',
                            placeholderValue,
                        )}
                    {/if}
                    <Button variant="outline" size="sm" onclick={addFn}
                        ><Plus class="mr-1 size-4" /> Add Mirror</Button
                    >
                </Card.Content>
            </Card.Root>
        {/if}
    {/if}
{/snippet}

{#snippet basicInfoContent()}
    <div class="grid gap-3 md:grid-cols-12">
        <Field.Field class="md:col-span-4">
            <Field.Label for="ep_no">Episode</Field.Label>
            <InputGroup.Root>
                <InputGroup.Addon>
                    <DropdownMenu.Root>
                        <DropdownMenu.Trigger>
                            {#snippet child({ props: triggerProps })}
                                <InputGroup.Button
                                    variant="default"
                                    {...triggerProps}
                                >
                                    {postTypes.find(
                                        (t) => t.value === form.postType,
                                    )?.label ?? 'Type'}
                                    <ChevronDown
                                        class="ml-1 size-4 opacity-50"
                                    />
                                </InputGroup.Button>
                            {/snippet}
                        </DropdownMenu.Trigger>
                        <DropdownMenu.Content class="min-w-40">
                            {#each postTypes as type (type.value)}
                                <DropdownMenu.Item
                                    onclick={() => (form.postType = type.value)}
                                >
                                    <div class="flex w-full items-center gap-2">
                                        {#if form.postType === type.value}<Check
                                                class="size-4"
                                            />{/if}
                                        <span>{type.label}</span>
                                    </div>
                                </DropdownMenu.Item>
                            {/each}
                        </DropdownMenu.Content>
                    </DropdownMenu.Root>
                </InputGroup.Addon>
                <InputGroup.Input
                    id="ep_no"
                    bind:value={form.epNo}
                    placeholder="1-4"
                />
            </InputGroup.Root>
        </Field.Field>
        <Field.Field class="md:col-span-8">
            <Field.Label for="title_lang">Title</Field.Label>
            <InputGroup.Root>
                <InputGroup.Addon>
                    <DropdownMenu.Root>
                        <DropdownMenu.Trigger>
                            {#snippet child({ props: triggerProps })}
                                <InputGroup.Button
                                    variant="default"
                                    {...triggerProps}
                                >
                                    {languages.find(
                                        (l) => l.value === currentLang,
                                    )?.label ?? 'Language'}
                                    <ChevronDown
                                        class="ml-1 size-4 opacity-50"
                                    />
                                </InputGroup.Button>
                            {/snippet}
                        </DropdownMenu.Trigger>
                        <DropdownMenu.Content>
                            {#each languages as lang (lang.value)}
                                <DropdownMenu.Item
                                    onclick={() => (currentLang = lang.value)}
                                >
                                    <div class="flex w-full items-center gap-2">
                                        {#if currentLang === lang.value}<Check
                                                class="size-4"
                                            />{/if}
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
    </div>
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
                            <InputGroup.Button
                                variant="default"
                                {...triggerProps}
                            >
                                {languages.find((l) => l.value === currentLang)
                                    ?.label ?? 'Language'}
                                <ChevronDown class="ml-1 size-4 opacity-50" />
                            </InputGroup.Button>
                        {/snippet}
                    </DropdownMenu.Trigger>
                    <DropdownMenu.Content>
                        {#each languages as lang (lang.value)}
                            <DropdownMenu.Item
                                onclick={() => (currentLang = lang.value)}
                            >
                                <div class="flex w-full items-center gap-2">
                                    {#if currentLang === lang.value}<Check
                                            class="size-4"
                                        />{/if}
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

{#snippet streamingAction()}
    {#if availableStreamTypes.length > 0}
        <DropdownMenu.Root>
            <DropdownMenu.Trigger>
                {#snippet child({ props: triggerProps })}
                    <Button variant="outline" size="sm" {...triggerProps}>
                        <Plus class="mr-1 size-4" /> Add Stream
                    </Button>
                {/snippet}
            </DropdownMenu.Trigger>
            <DropdownMenu.Content class="min-w-40">
                {#each availableStreamTypes as st (st.value)}
                    <DropdownMenu.Item onclick={() => addStream(st.value)}
                        ><span>{st.label}</span></DropdownMenu.Item
                    >
                {/each}
            </DropdownMenu.Content>
        </DropdownMenu.Root>
    {/if}
{/snippet}

{#snippet streamingContent()}
    <div class="space-y-3">
        {@render streamCard(
            'Embed',
            form.embed,
            clearEmbed,
            addEmbedMirror,
            deleteEmbedMirror,
            '<iframe src=...></iframe>',
        )}
        {@render streamCard(
            'Stream (M3U8)',
            form.saluran,
            clearSaluran,
            addStreamMirror,
            deleteStreamMirror,
            'https://...m3u8',
        )}
        {#if !hasEmbed && !hasSaluran}
            <p>No stream configured.</p>
        {/if}
    </div>
{/snippet}

{#snippet downloadLinksAction()}
    <Button variant="outline" size="sm" onclick={addLink}
        ><Plus class="mr-1 size-4" /> Add File</Button
    >
{/snippet}

{#snippet downloadLinksContent()}
    <div class="space-y-3">
        {#each form.links as link, i (i)}
            {#if isMobile}
                <div class="space-y-3">
                    <Field.Field orientation="horizontal">
                        <Field.Label>Filename</Field.Label>
                        <Input
                            bind:value={form.links[i].name}
                            placeholder="Filename"
                        />
                        <Button
                            variant="ghost"
                            size="icon"
                            class="text-destructive shrink-0"
                            onclick={() => deleteLink(i)}
                            ><Trash2 class="size-4" /></Button
                        >
                    </Field.Field>
                    {#if link.value.length === 0}
                        <p>No mirrors.</p>
                    {:else}
                        {@render mirrorFields(
                            form.links[i],
                            (j) => deleteMirror(i, j),
                            i,
                            'Mirror name',
                            'https://...',
                        )}
                    {/if}
                    <Button
                        variant="outline"
                        size="sm"
                        onclick={() => addMirror(i)}
                        ><Plus class="mr-1 size-4" /> Add Mirror</Button
                    >
                </div>
            {:else}
                <Card.Root>
                    {@render resourceHeader(
                        form.links[i].name || 'New File',
                        () => deleteLink(i),
                    )}
                    <Card.Content class="space-y-3">
                        <Field.Field>
                            <Field.Label>Filename</Field.Label>
                            <Input
                                bind:value={form.links[i].name}
                                placeholder="Filename"
                            />
                        </Field.Field>
                        {#if link.value.length === 0}
                            <p>No mirrors.</p>
                        {:else}
                            {@render mirrorFields(
                                form.links[i],
                                (j) => deleteMirror(i, j),
                                i,
                                'Mirror name',
                                'https://...',
                            )}
                        {/if}
                        <Button
                            variant="outline"
                            size="sm"
                            onclick={() => addMirror(i)}
                            ><Plus class="mr-1 size-4" /> Add Mirror</Button
                        >
                    </Card.Content>
                </Card.Root>
            {/if}
        {/each}
        {#if form.links.length === 0}
            <p>No files.</p>
        {/if}
    </div>
{/snippet}

{#snippet thumbnailAction()}
    <MediaManager title="Thumbnail" bind:value={form.thumbnailItem}>
        {#snippet header({ title: _title, onOpen })}
            <MediaManagerTrigger
                hasValue={form.thumbnailItem !== null}
                onclick={onOpen}
            />
        {/snippet}
        {#snippet empty()}{/snippet}
    </MediaManager>
{/snippet}

{#snippet thumbnailContent()}
    {#if form.thumbnailItem}
        <img
            src={form.thumbnailItem.mediumUrl ?? form.thumbnailItem.url}
            alt={form.thumbnailItem.filename}
        />
    {:else}
        <p>Click to open media picker</p>
    {/if}
{/snippet}

{#snippet animeAction()}
    <Button
        variant="ghost"
        size="icon"
        onclick={() => (showAnimeImage = !showAnimeImage)}
    >
        {#if showAnimeImage}<ChevronUp class="size-4" />{:else}<ChevronDown
                class="size-4"
            />{/if}
    </Button>
{/snippet}

{#snippet animeContent()}
    {#if showAnimeImage && metadata?.coverImage?.extraLarge}
        <img src={metadata.coverImage.extraLarge} alt="Cover" />
    {/if}
    <div>
        <p>Title</p>
        <Link href={animeShow.url(anime.id)}>{anime.title.en}</Link>
    </div>
    <div>
        <p>Links</p>
        <div class="flex gap-1">
            {#if metadata?.id}
                <a
                    href="https://anilist.co/anime/{metadata.id}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <Badge variant="secondary"
                        >Anilist<ExternalLink class="ml-1 size-3" /></Badge
                    >
                </a>
            {/if}
            {#if metadata?.idMal}
                <a
                    href="https://myanimelist.net/anime/{metadata.idMal}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <Badge variant="secondary"
                        >MyAnimelist<ExternalLink class="ml-1 size-3" /></Badge
                    >
                </a>
            {/if}
        </div>
    </div>
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
        <Field.Error>{form.errors.isPublished}</Field.Error>
    </Field.Field>
{/snippet}

<div class="p-4 w-full mx-auto max-w-400">
    <form onsubmit={submit}>
        <div class="grid gap-4 md:gap-6 md:grid-cols-12">
            <div class="md:col-span-8 space-y-6">
                {@render card('Basic Information', basicInfoContent)}
                {@render card('Streaming', streamingContent, streamingAction)}
                {@render card(
                    'Download Links',
                    downloadLinksContent,
                    downloadLinksAction,
                )}
            </div>
            <div
                class="md:col-span-4 md:sticky md:top-4 md:self-start space-y-6"
            >
                {@render card('Thumbnail', thumbnailContent, thumbnailAction)}
                {@render card('Anime', animeContent, animeAction)}
                {@render card('Publishing', publishingContent)}
                <div class="flex gap-2">
                    {#if post}
                        <Button
                            type="button"
                            variant="destructive"
                            size="icon"
                            onclick={deletePost}
                            disabled={form.processing}
                        >
                            <Trash2 class="size-4" />
                        </Button>
                    {/if}
                    {#if form.isDirty}
                        <Button
                            type="button"
                            variant="outline"
                            onclick={() => form.reset()}
                            disabled={form.processing}>Reset</Button
                        >
                    {/if}
                    <Button type="submit" disabled={form.processing}>
                        {form.processing
                            ? 'Saving...'
                            : post
                              ? 'Update'
                              : 'Save'}
                    </Button>
                    {#if canPublish && !post?.isPublished}
                        <Button
                            type="button"
                            variant="secondary"
                            onclick={publish}
                            disabled={form.processing}
                        >
                            {form.processing ? 'Publishing...' : 'Publish'}
                        </Button>
                    {/if}
                </div>
            </div>
        </div>
    </form>
</div>
