<script module lang="ts">
    import { index as animeIndex } from '@/routes/anime';

    export const layout = {
        breadcrumbs: [
            { title: 'Anime', href: animeIndex().url },
            { title: 'Create Episode', href: '' },
        ],
    };
</script>

<script lang="ts">
    import { router, useForm } from '@inertiajs/svelte';
    import Check from 'lucide-svelte/icons/check';
    import ChevronDown from 'lucide-svelte/icons/chevron-down';
    import ChevronUp from 'lucide-svelte/icons/chevron-up';
    import ExternalLink from 'lucide-svelte/icons/external-link';
    import Pencil from 'lucide-svelte/icons/pencil';
    import Plus from 'lucide-svelte/icons/plus';
    import Send from 'lucide-svelte/icons/send';
    import Trash2 from 'lucide-svelte/icons/trash-2';
    import PostController from '@/actions/App/Http/Controllers/PostController';
    import AppHead from '@/components/app-head.svelte';
    import InputError from '@/components/input-error.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import * as Card from '@/components/ui/card/index.js';
    import * as DropdownMenu from '@/components/ui/dropdown-menu/index.js';
    import { Input } from '@/components/ui/input';
    import * as InputGroup from '@/components/ui/input-group/index.js';
    import { Label } from '@/components/ui/label';
    import { Switch } from '@/components/ui/switch';
    import { Textarea } from '@/components/ui/textarea';
    import { useDisplay } from '@/lib/use-display.svelte';

    type PostFormData = App.Data.Anime.PostFormData;
    type AnilistMediaData = App.Data.Anilist.AnilistMediaData;

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
    const pageTitle = $derived(
        isEditing ? `Editing Episode` : 'Create Episode',
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

    const form = $state(
        useForm<PostFormData>({
            title: post?.title ?? { en: '', id: '', romaji: '', native: '' },
            description: post?.description ?? { en: '', id: '' },
            postType: post?.postType ?? 'tv',
            epNo: post?.epNo ?? '',
            isPublished: post?.isPublished ?? false,
            links: post?.links ?? [],
            thumbnailItem: post?.thumbnailItem ?? null,
        }),
    );

    const title = $derived(
        isEditing
            ? `Editing ${post?.epNo ? 'Ep ' + post.epNo : 'Episode'}`
            : 'Create Episode',
    );

    function submit(e: Event) {
        e.preventDefault();

        if (isEditing && post?.id) {
            form.put(PostController.update(anime.id, post.id).url, {
                preserveScroll: true,
            });
        } else {
            form.post(PostController.store(anime.id).url);
        }
    }

    function publish() {
        form.isPublished = true;
        submit(new Event('submit'));
    }

    function save() {
        submit(new Event('submit'));
    }

    function deletePost() {
        if (!post?.id) {
            return;
        }

        if (confirm('Are you sure you want to delete this episode?')) {
            router.delete(PostController.destroy(anime.id, post.id).url);
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

<div class="p-4 w-full mx-auto max-w-400">
    <form onsubmit={submit}>
        <div class="grid gap-4 md:gap-6 md:grid-cols-12">
            <div class="space-y-6 md:col-span-8">
                <Card.Root>
                    <Card.Content class="space-y-4">
                        <Card.Title>Basic Information</Card.Title>

                        <div class="flex items-start gap-4">
                            <div class="flex-none w-32">
                                <Label for="ep_no">Ep No.</Label>
                                <Input
                                    id="ep_no"
                                    bind:value={form.epNo}
                                    placeholder="1-4"
                                />
                            </div>

                            <div class="flex-none w-36">
                                <Label>Title Language</Label>
                                <DropdownMenu.Root>
                                    <DropdownMenu.Trigger>
                                        {#snippet child({
                                            props: triggerProps,
                                        })}
                                            <Button
                                                variant="outline"
                                                class="w-full justify-between"
                                                {...triggerProps}
                                            >
                                                {languages.find(
                                                    (l) =>
                                                        l.value === currentLang,
                                                )?.label ?? 'Language'}
                                                <ChevronDown
                                                    class="size-4 opacity-50"
                                                />
                                            </Button>
                                        {/snippet}
                                    </DropdownMenu.Trigger>
                                    <DropdownMenu.Content>
                                        {#each languages as lang (lang.value)}
                                            <DropdownMenu.Item
                                                onclick={() =>
                                                    (currentLang = lang.value)}
                                            >
                                                <div
                                                    class="flex w-full items-center gap-2"
                                                >
                                                    {#if currentLang === lang.value}
                                                        <Check class="size-4" />
                                                    {/if}
                                                    <span>{lang.label}</span>
                                                </div>
                                            </DropdownMenu.Item>
                                        {/each}
                                    </DropdownMenu.Content>
                                </DropdownMenu.Root>
                            </div>

                            <div class="flex-none w-36">
                                <Label>Ep. Type</Label>
                                <DropdownMenu.Root>
                                    <DropdownMenu.Trigger>
                                        {#snippet child({
                                            props: triggerProps,
                                        })}
                                            <Button
                                                variant="outline"
                                                class="w-full justify-between"
                                                {...triggerProps}
                                            >
                                                {postTypes.find(
                                                    (t) =>
                                                        t.value ===
                                                        form.postType,
                                                )?.label ?? 'Type'}
                                                <ChevronDown
                                                    class="size-4 opacity-50"
                                                />
                                            </Button>
                                        {/snippet}
                                    </DropdownMenu.Trigger>
                                    <DropdownMenu.Content>
                                        {#each postTypes as type (type.value)}
                                            <DropdownMenu.Item
                                                onclick={() =>
                                                    (form.postType =
                                                        type.value)}
                                            >
                                                <div
                                                    class="flex w-full items-center gap-2"
                                                >
                                                    {#if form.postType === type.value}
                                                        <Check class="size-4" />
                                                    {/if}
                                                    <span>{type.label}</span>
                                                </div>
                                            </DropdownMenu.Item>
                                        {/each}
                                    </DropdownMenu.Content>
                                </DropdownMenu.Root>
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="title_lang">
                                Title ({currentLang})
                            </Label>
                            <Input
                                id="title_lang"
                                bind:value={form.title[currentLang]}
                                placeholder={`Title in ${languages.find((l) => l.value === currentLang)?.label}`}
                            />
                            <InputError
                                message={form.errors[`title.${currentLang}`]}
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="title_romaji">Title (Romaji)</Label>
                            <Input
                                id="title_romaji"
                                bind:value={form.title.romaji}
                                placeholder="Title in romaji"
                            />
                            <InputError message={form.errors['title.romaji']} />
                        </div>

                        <div class="grid gap-2">
                            <Label for="title_native">Title (Native)</Label>
                            <Input
                                id="title_native"
                                bind:value={form.title.native}
                                placeholder="Title in native script"
                            />
                            <InputError message={form.errors['title.native']} />
                        </div>

                        <div class="grid gap-2">
                            <Label for="description_lang">
                                Description ({currentLang})
                            </Label>
                            <Textarea
                                id="description_lang"
                                bind:value={form.description[currentLang]}
                                rows={6}
                                placeholder={`Description in ${languages.find((l) => l.value === currentLang)?.label}`}
                            />
                            <InputError
                                message={form.errors[
                                    `description.${currentLang}`
                                ]}
                            />
                        </div>
                    </Card.Content>
                </Card.Root>

                <Card.Root>
                    <Card.Header
                        class="flex flex-row items-center justify-between"
                    >
                        <Card.Title>Links</Card.Title>
                        <Button
                            variant="outline"
                            size="sm"
                            onclick={addFilename}
                        >
                            <Plus class="mr-1 size-4" />
                            Add
                        </Button>
                    </Card.Header>
                    <Card.Content class="flex flex-col gap-4">
                        {#each form.links as resource, i (i)}
                            <Card.Root variant="outlined">
                                <Card.Header
                                    class="flex flex-row items-center justify-between py-2"
                                >
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
                                        <Card.Title class="text-sm">
                                            {resource.name}
                                        </Card.Title>
                                    </div>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="size-8"
                                        onclick={() => addLink(i)}
                                    >
                                        <Plus class="size-4" />
                                    </Button>
                                </Card.Header>
                                <Card.Content class="pt-0">
                                    {#if resource.value.length === 0}
                                        <p
                                            class="text-sm text-muted-foreground"
                                        >
                                            No data.
                                        </p>
                                    {:else}
                                        {#each resource.value as link, j (j)}
                                            <div
                                                class="grid grid-cols-[80px_1fr] gap-2 items-start mb-2"
                                            >
                                                <Label
                                                    class="mt-2 text-muted-foreground"
                                                >
                                                    Name
                                                </Label>
                                                <Input
                                                    bind:value={
                                                        form.links[i].value[j]
                                                            .name
                                                    }
                                                    placeholder="Gudang"
                                                />
                                            </div>
                                            <div
                                                class="grid grid-cols-[80px_1fr] gap-2 items-start mb-2"
                                            >
                                                <Label
                                                    class="mt-2 text-muted-foreground"
                                                >
                                                    Link
                                                </Label>
                                                <div class="space-y-2">
                                                    <Textarea
                                                        bind:value={
                                                            form.links[i].value[
                                                                j
                                                            ].value
                                                        }
                                                        placeholder="https://..."
                                                        rows={2}
                                                    />
                                                    <Button
                                                        variant="destructive"
                                                        size="sm"
                                                        onclick={() =>
                                                            deleteLink(i, j)}
                                                    >
                                                        Delete
                                                    </Button>
                                                </div>
                                            </div>
                                        {/each}
                                    {/if}
                                </Card.Content>
                            </Card.Root>
                        {/each}

                        {#if form.links.length === 0}
                            <p class="text-sm text-muted-foreground">
                                No data.
                            </p>
                        {/if}
                    </Card.Content>
                </Card.Root>
            </div>

            <div
                class="space-y-6 md:col-span-4 md:sticky md:top-4 md:self-start"
            >
                <Card.Root>
                    <Card.Header
                        class="flex flex-row items-center justify-between"
                    >
                        <Card.Title>Anime</Card.Title>
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
                    </Card.Header>
                    {#if showAnimeImage && metadata?.coverImage?.extraLarge}
                        <div class="px-6">
                            <img
                                src={metadata.coverImage.extraLarge}
                                alt="Cover"
                                class="w-full rounded-lg object-cover"
                            />
                        </div>
                    {/if}
                    <Card.Content class="space-y-4">
                        <div>
                            <p class="text-sm font-medium leading-none mb-1">
                                Title
                            </p>
                            <a
                                href="/anime/{anime.id}"
                                class="text-sm text-primary underline-offset-4 hover:underline"
                            >
                                {anime.title.en}
                            </a>
                        </div>
                        <div>
                            <p class="text-sm font-medium leading-none mb-2">
                                Links
                            </p>
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
                    </Card.Content>
                </Card.Root>

                <Card.Root>
                    <Card.Content class="space-y-4">
                        <Card.Title>Publishing</Card.Title>
                        <div class="flex items-center gap-2">
                            <Switch
                                checked={form.isPublished}
                                onCheckedChange={(v) => (form.isPublished = v)}
                                id="is_published"
                            />
                            <Label for="is_published">Published</Label>
                        </div>
                        <InputError message={form.errors.isPublished} />
                    </Card.Content>
                </Card.Root>

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
                    <Button
                        type="button"
                        variant="outline"
                        onclick={() => form.reset()}
                        disabled={form.processing}
                    >
                        Reset
                    </Button>
                    <Button
                        type="button"
                        variant={post?.isPublished ? 'default' : 'outline'}
                        onclick={save}
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
