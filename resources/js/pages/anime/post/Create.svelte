<script lang="ts">
    import {Link, router, setLayoutProps, useForm} from '@inertiajs/svelte';
    import Check from 'lucide-svelte/icons/check';
    import ChevronDown from 'lucide-svelte/icons/chevron-down';
    import ChevronUp from 'lucide-svelte/icons/chevron-up';
    import ExternalLink from 'lucide-svelte/icons/external-link';
    import Pencil from 'lucide-svelte/icons/pencil';
    import Plus from 'lucide-svelte/icons/plus';
    import Trash2 from 'lucide-svelte/icons/trash-2';
    import type { Snippet } from 'svelte';
    import PostController from '@/actions/App/Http/Controllers/PostController';
    import AppHead from '@/components/app-head.svelte';
    import InputError from '@/components/input-error.svelte';
    import MediaManager from '@/components/media-manager/index.svelte';
    import MediaManagerTrigger from '@/components/media-manager/media-manager-trigger.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardTitle,
    } from '@/components/ui/card';
    import * as DropdownMenu from '@/components/ui/dropdown-menu/index.js';
    import { Input } from '@/components/ui/input';
    import * as InputGroup from '@/components/ui/input-group/index.js';
    import { Label } from '@/components/ui/label';
    import { Switch } from '@/components/ui/switch';
    import { Textarea } from '@/components/ui/textarea';
    import { t } from '@/lib/locale.svelte';
    import { useDisplay } from '@/lib/use-display.svelte';
    import { index as animeIndex, show as animeShow } from '@/routes/anime';

    type PostFormData = App.Data.Anime.PostFormData;

    type Props = App.Data.Anime.PostCreateResponse;

    let {
        anime,
        post = null,
        canPublish = false,
        metadata = null,
    }: Props = $props();

    const { mdAndDown } = useDisplay();
    const isMobile = $derived(mdAndDown.current);

    const isEditing = $derived(Boolean(post));
    const postTitle = $derived(
        post
            ? post.postType === 'tv' && post.epNo
                ? `Episode ${post.epNo}: ${t(post.title)}`
                : t(post.title)
            : null,
    );
    const pageTitle = $derived(
        isEditing && postTitle ? `Editing ${postTitle}` : 'Create Episode',
    );

    let currentLang = $state<'en' | 'id'>('en');
    let showAnimeImage = $state(false);
    let thumbnailMedia = $state<App.Data.MediaData | null>(null);

    const languages: { label: string; value: 'en' | 'id' }[] = [
        { label: 'English', value: 'en' },
        { label: 'Indonesian', value: 'id' },
    ];

    const postTypes = [
        { value: 'tv', label: 'TV Ep' },
        { value: 'bd', label: 'Blu-ray BOX/DISC' },
        { value: 'movie', label: 'Movie' },
    ];

    // svelte-ignore state_referenced_locally
    const form = $state(
        useForm<PostFormData>({
            id: post?.id ?? null,
            title: post?.title ?? { en: '', id: '', romaji: '', native: '' },
            description: post?.description ?? { en: '', id: '' },
            postType: post?.postType ?? 'tv',
            epNo: post?.epNo ?? '',
            isPublished: post?.isPublished ?? false,
            canPublish: post?.canPublish ?? false,
            links: post?.links ?? [],
            thumbnailItem: post?.thumbnailItem ?? null,
        }),
    );

    $effect(() => {
        if (postTitle) {
            setLayoutProps({
                title: `Editing ${postTitle}`,
                breadcrumbs: [
                    { title: 'Anime', href: animeIndex() },
                    { title: t(anime.title), href: animeShow.url(anime.id) },
                    { title: postTitle, href: '' },
                    { title: 'Edit', href: '' },
                ],
            });
        } else {
            setLayoutProps({
                title: 'Create Episode',
                breadcrumbs: [
                    { title: 'Anime', href: animeIndex() },
                    { title: t(anime.title), href: animeShow.url(anime.id) },
                    { title: 'Create Episode', href: '' },
                ],
            });
        }
    });

    $effect(() => {
        if (post?.thumbnailItem && !thumbnailMedia) {
            thumbnailMedia =
                post.thumbnailItem as unknown as App.Data.MediaData;
        }
    });

    $effect(() => {
        form.thumbnailItem = thumbnailMedia
            ? [{ id: thumbnailMedia.id }]
            : null;
    });

    function submit(e: Event) {
        e.preventDefault();

        if (isEditing && post?.id) {
            form.put(
                PostController.update({ anime: anime.id, post: post.id }).url,
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
        if (!post?.id) {
            return;
        }

        if (confirm('Are you sure you want to delete this episode?')) {
            router.delete(
                PostController.destroy({ anime: anime.id, post: post.id }).url,
            );
        }
    }

    function addFilename() {
        const name = window.prompt('Enter filename');

        if (name) {
            form.links = [
                ...form.links,
                { name, type: 'link', value: [{ name: '', value: '' }] },
            ];
        }
    }

    function editFilename(index: number) {
        const name = window.prompt('Edit filename', form.links[index].name);

        if (name !== null) {
            form.links[index].name = name;
        }
    }

    function deleteFilename(index: number) {
        form.links = form.links.filter((_, i) => i !== index);
    }

    function addLink(index: number) {
        form.links[index].value = [
            ...form.links[index].value,
            { name: '', value: '' },
        ];
    }

    function deleteLink(i: number, j: number) {
        form.links[i].value = form.links[i].value.filter((_, k) => k !== j);
    }
</script>

<AppHead title={pageTitle} />

{#snippet card(content: Snippet)}
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
    <div class="grid gap-2 md:grid-cols-12">
        <div class="md:col-span-4 grid gap-2">
            <Label>Episode</Label>
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
                                        {#if form.postType === type.value}
                                            <Check class="size-4" />
                                        {/if}
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
        </div>
        <div class="md:col-span-8 grid gap-2">
            <Label for="title_lang">Title</Label>
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
                                </InputGroup.Button>
                            {/snippet}
                        </DropdownMenu.Trigger>
                        <DropdownMenu.Content>
                            {#each languages as lang (lang.value)}
                                <DropdownMenu.Item
                                    onclick={() => (currentLang = lang.value)}
                                >
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
            <InputError message={form.errors[`title.${currentLang}`]} />
        </div>
    </div>
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
        <Label for="description_lang">Description</Label>
        <div class="flex items-center gap-2">
            {#each languages as lang (lang.value)}
                <Button
                    type="button"
                    variant={currentLang === lang.value ? 'default' : 'outline'}
                    size="sm"
                    onclick={() => (currentLang = lang.value)}
                >
                    {lang.label}
                </Button>
            {/each}
        </div>
        <Textarea
            id="description_lang"
            bind:value={form.description[currentLang]}
            rows={6}
            placeholder={`Description in ${languages.find((l) => l.value === currentLang)?.label}`}
        />
        <InputError message={form.errors[`description.${currentLang}`]} />
    </div>
{/snippet}

{#snippet linksContent()}
    <div class="flex items-center justify-between">
        <CardTitle>Links</CardTitle>
        <Button variant="outline" size="sm" onclick={addFilename}>
            <Plus class="mr-1 size-4" />
            Add
        </Button>
    </div>
    <div class="flex flex-col gap-4">
        {#each form.links as resource, i (i)}
            <div class="rounded-lg border p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Button
                            variant="ghost"
                            size="icon"
                            class="size-8"
                            onclick={() => editFilename(i)}
                        >
                            <Pencil class="size-4" />
                        </Button>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="size-8 text-destructive"
                            onclick={() => deleteFilename(i)}
                        >
                            <Trash2 class="size-4" />
                        </Button>
                        <span class="text-sm font-medium">{resource.name}</span>
                    </div>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="size-8"
                        onclick={() => addLink(i)}
                    >
                        <Plus class="size-4" />
                    </Button>
                </div>
                {#if resource.value.length === 0}
                    <p class="text-sm text-muted-foreground">No data.</p>
                {:else}
                    {#each resource.value as _, j (j)}
                        <div
                            class="grid grid-cols-[80px_1fr] gap-2 items-start"
                        >
                            <Label class="mt-2 text-muted-foreground">
                                Name
                            </Label>
                            <Input
                                bind:value={form.links[i].value[j].name}
                                placeholder="Gudang"
                            />
                        </div>
                        <div
                            class="grid grid-cols-[80px_1fr] gap-2 items-start"
                        >
                            <Label class="mt-2 text-muted-foreground">
                                Link
                            </Label>
                            <div class="space-y-2">
                                <Textarea
                                    bind:value={form.links[i].value[j].value}
                                    placeholder="https://..."
                                    rows={2}
                                />
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    onclick={() => deleteLink(i, j)}
                                >
                                    Delete
                                </Button>
                            </div>
                        </div>
                    {/each}
                {/if}
            </div>
        {/each}
        {#if form.links.length === 0}
            <p class="text-sm text-muted-foreground">No data.</p>
        {/if}
    </div>
{/snippet}

{#snippet thumbnailContent()}
    <div class="flex items-center justify-between">
        <CardTitle>Thumbnail</CardTitle>
        <MediaManager title="Thumbnail" bind:value={thumbnailMedia}>
            {#snippet header({ title: _title, onOpen })}
                <MediaManagerTrigger
                    hasValue={thumbnailMedia !== null}
                    onclick={onOpen}
                />
            {/snippet}
            {#snippet empty()}{/snippet}
        </MediaManager>
    </div>
    {#if thumbnailMedia}
        <img
            src={thumbnailMedia.mediumUrl ?? thumbnailMedia.url}
            alt={thumbnailMedia.filename}
            class="h-20 w-20 rounded object-cover"
        />
    {:else}
        <p class="text-sm text-muted-foreground">Click to open media picker</p>
    {/if}
{/snippet}

{#snippet animeContent()}
    <div class="flex items-center justify-between">
        <CardTitle>Anime</CardTitle>
        <Button
            variant="ghost"
            size="icon"
            class="size-8"
            onclick={() => (showAnimeImage = !showAnimeImage)}
        >
            {#if showAnimeImage}
                <ChevronUp class="size-4" />
            {:else}
                <ChevronDown class="size-4" />
            {/if}
        </Button>
    </div>
    {#if showAnimeImage && metadata?.coverImage?.extraLarge}
        <img
            src={metadata.coverImage.extraLarge}
            alt="Cover"
            class="mx-auto rounded-lg max-h-80"
        />
    {/if}
    <div>
        <p class="text-sm font-medium leading-none mb-1">Title</p>
        <Link
            href={animeShow.url(anime.id)}
            class="text-sm text-primary underline-offset-4 hover:underline"
        >
            {anime.title.en}
        </Link>
    </div>
    <div>
        <p class="text-sm font-medium leading-none mb-2">Links</p>
        <div class="flex gap-1">
            {#if metadata?.id}
                <a
                    href="https://anilist.co/anime/{metadata.id}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <Badge variant="secondary">
                        Anilist
                        <ExternalLink class="ml-1 size-3" />
                    </Badge>
                </a>
            {/if}
            {#if metadata?.idMal}
                <a
                    href="https://myanimelist.net/anime/{metadata.idMal}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <Badge variant="secondary">
                        MyAnimelist
                        <ExternalLink class="ml-1 size-3" />
                    </Badge>
                </a>
            {/if}
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

<div class="p-4 w-full mx-auto max-w-400">
    <form onsubmit={submit}>
        <div class="grid gap-4 md:gap-6 md:grid-cols-12">
            <div class="space-y-6 md:col-span-8">
                {@render card(basicInfoContent)}
                {@render card(linksContent)}
            </div>

            <div
                class="space-y-6 md:col-span-4 md:sticky md:top-4 md:self-start"
            >
                {@render card(thumbnailContent)}
                {@render card(animeContent)}
                {@render card(publishingContent)}

                <div class="flex gap-2">
                    {#if isEditing}
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
                    {#if canPublish && !post?.isPublished}
                        <Button
                            type="button"
                            variant={post?.isPublished
                                ? 'default'
                                : 'secondary'}
                            onclick={publish}
                            disabled={form.processing}
                            class="flex-1"
                        >
                            {form.processing ? 'Publishing...' : 'Publish'}
                        </Button>
                    {/if}
                </div>
            </div>
        </div>
    </form>
</div>
