<script lang="ts">
    import { router, setLayoutProps, useForm } from '@inertiajs/svelte';
    import { untrack } from 'svelte';
    import type { Snippet } from 'svelte';
    import ShinraiPostController from '@/actions/App/Http/Controllers/ShinraiPostController';
    import AppHead from '@/components/app-head.svelte';
    import { Icon } from '@/components/icon';
    import MediaManager from '@/components/media-manager/index.svelte';
    import MediaManagerTrigger from '@/components/media-manager/media-manager-trigger.svelte';
    import TracksEditor from '@/components/tracks-editor.svelte';
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
    import { index as albumIndex } from '@/routes/album';
    import { index as mvIndex } from '@/routes/mv';

    type Props = App.Data.Music.ShinraiCreateResponse;
    type FormData = App.Data.Music.ShinraiStoreData;

    let { type = 'mv', post, canPublish = false }: Props = $props();

    const { mdAndDown } = useDisplay();
    const isMobile = $derived(mdAndDown.current);

    const isMv = $derived((post?.postType ?? type) === 'mv');
    const entityLabel = $derived(isMv ? 'Music Video' : 'Album');

    const pageTitle = $derived(
        post ? `Editing ${t(post.title)}` : `Create ${entityLabel}`,
    );

    let currentLang = $state<'en' | 'id'>('en');

    const languages: { label: string; value: 'en' | 'id' }[] = [
        { label: 'English', value: 'en' },
        { label: 'Indonesian', value: 'id' },
    ];

    const postTypes = [
        { value: 'mv', label: 'Music Video' },
        { value: 'album', label: 'Album' },
        { value: 'single', label: 'Single' },
    ];

    const streamTypes = [
        { value: 'saluran', label: 'Stream (M3U8)' },
        { value: 'embed', label: 'Embed' },
    ] as const;

    const form = untrack(() =>
        useForm<FormData>({
            title: post?.title ?? { en: '', id: '', romaji: '', native: '' },
            description: post?.description ?? { en: '', id: '' },
            postType: post?.postType ?? type ?? 'mv',
            isPublished: post?.isPublished ?? false,
            links: (post?.links as App.Data.Anime.ResourceData[]) ?? [],
            embed: (post?.embed as App.Data.Anime.ResourceData | null) ?? null,
            saluran:
                (post?.saluran as App.Data.Anime.ResourceData | null) ?? null,
            thumbnailItem:
                (post?.thumbnailItem as App.Data.MediaData | null) ?? null,
            vgmdbData:
                (post?.vgmdbData as Record<string, any> | null) ?? null,
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

    function addStream(streamType: 'embed' | 'saluran') {
        if (streamType === 'embed') {
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
        const parentRoute = isMv ? mvIndex().url : albumIndex().url;

        if (post) {
            setLayoutProps({
                title: pageTitle,
                breadcrumbs: [
                    { title: entityLabel, href: parentRoute },
                    { title: t(post.title), href: '' },
                    { title: 'Edit', href: '' },
                ],
            });
        } else {
            setLayoutProps({
                title: pageTitle,
                breadcrumbs: [
                    { title: entityLabel, href: parentRoute },
                    { title: `Create ${entityLabel}`, href: '' },
                ],
            });
        }
    });

    function submit(e: Event) {
        e.preventDefault();

        if (post?.id) {
            form.put(ShinraiPostController.update(post.id).url, {
                preserveScroll: true,
            });
        } else {
            form.post(ShinraiPostController.store().url);
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

        if (confirm(`Are you sure you want to delete this ${entityLabel}?`)) {
            router.delete(ShinraiPostController.destroy(post.id).url);
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
    placeholderName?: string,
    placeholderValue?: string,
)}
    {#each resource.value as _, j (j)}
        <div class="flex items-start gap-2">
            <div class="flex-1 space-y-3">
                <Field.Field>
                    <Field.Label>{placeholderName ?? 'Mirror name'}</Field.Label>
                    <Input
                        bind:value={resource.value[j].name}
                        placeholder={placeholderName ?? 'Mirror name'}
                    />
                </Field.Field>
                <Field.Field>
                    <Field.Label
                        >{placeholderValue?.startsWith('<')
                            ? 'Embed Code / URL'
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
                <Icon
                    lucide={() => import('lucide-svelte/icons/trash-2')}
                    tabler={() => import('@tabler/icons-svelte/icons/trash')}
                    hugeicons={() =>
                        import('@hugeicons/core-free-icons/Delete02Icon')}
                    phosphor={() => import('phosphor-svelte/lib/TrashIcon')}
                    remixicon={() =>
                        import('remixicon-svelte/icons/delete-bin-line')}
                    class="size-4"
                />
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
                <Icon
                    lucide={() => import('lucide-svelte/icons/trash-2')}
                    tabler={() => import('@tabler/icons-svelte/icons/trash')}
                    hugeicons={() =>
                        import('@hugeicons/core-free-icons/Delete02Icon')}
                    phosphor={() => import('phosphor-svelte/lib/TrashIcon')}
                    remixicon={() =>
                        import('remixicon-svelte/icons/delete-bin-line')}
                    class="size-4"
                />
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
                    <p class="text-xs text-muted-foreground">No mirrors.</p>
                {:else}
                    {@render mirrorFields(
                        resource,
                        deleteFn,
                        'Mirror name',
                        placeholderValue,
                    )}
                {/if}
                <Button variant="outline" size="sm" onclick={addFn}>
                    <Icon
                        lucide={() => import('lucide-svelte/icons/plus')}
                        tabler={() => import('@tabler/icons-svelte/icons/plus')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/PlusSignIcon')}
                        phosphor={() => import('phosphor-svelte/lib/PlusIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/add-line')}
                        class="mr-1 size-4"
                    />
                    Add Mirror
                </Button>
            </div>
        {:else}
            <Card.Root>
                {@render resourceHeader(title, clearFn)}
                <Card.Content class="space-y-3">
                    {#if resource.value.length === 0}
                        <p class="text-xs text-muted-foreground">No mirrors.</p>
                    {:else}
                        {@render mirrorFields(
                            resource,
                            deleteFn,
                            'Mirror name',
                            placeholderValue,
                        )}
                    {/if}
                    <Button variant="outline" size="sm" onclick={addFn}>
                        <Icon
                            lucide={() => import('lucide-svelte/icons/plus')}
                            tabler={() =>
                                import('@tabler/icons-svelte/icons/plus')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/PlusSignIcon')}
                            phosphor={() =>
                                import('phosphor-svelte/lib/PlusIcon')}
                            remixicon={() =>
                                import('remixicon-svelte/icons/add-line')}
                            class="mr-1 size-4"
                        />
                        Add Mirror
                    </Button>
                </Card.Content>
            </Card.Root>
        {/if}
    {/if}
{/snippet}

{#snippet basicInfoContent()}
    <div class="space-y-4">
        <Field.Field>
            <Field.Label for="post_type">Type</Field.Label>
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
                                    <Icon
                                        lucide={() =>
                                            import('lucide-svelte/icons/chevron-down')}
                                        tabler={() =>
                                            import('@tabler/icons-svelte/icons/chevron-down')}
                                        hugeicons={() =>
                                            import('@hugeicons/core-free-icons/ArrowDown01Icon')}
                                        phosphor={() =>
                                            import('phosphor-svelte/lib/CaretDownIcon')}
                                        remixicon={() =>
                                            import('remixicon-svelte/icons/arrow-down-s-line')}
                                        class="ml-1 size-4 opacity-50"
                                    />
                                </InputGroup.Button>
                            {/snippet}
                        </DropdownMenu.Trigger>
                        <DropdownMenu.Content class="min-w-40">
                            {#each postTypes as pType (pType.value)}
                                <DropdownMenu.Item
                                    onclick={() =>
                                        (form.postType = pType.value)}
                                >
                                    <div class="flex w-full items-center gap-2">
                                        {#if form.postType === pType.value}
                                            <Icon
                                                lucide={() =>
                                                    import('lucide-svelte/icons/check')}
                                                tabler={() =>
                                                    import('@tabler/icons-svelte/icons/check')}
                                                hugeicons={() =>
                                                    import('@hugeicons/core-free-icons/Tick02Icon')}
                                                phosphor={() =>
                                                    import('phosphor-svelte/lib/CheckIcon')}
                                                remixicon={() =>
                                                    import('remixicon-svelte/icons/check-line')}
                                                class="size-4"
                                            />
                                        {/if}
                                        <span>{pType.label}</span>
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

        <div class="grid gap-4 md:grid-cols-2">
            <Field.Field>
                <Field.Label for="title_romaji">Title (Romaji)</Field.Label>
                <Input
                    id="title_romaji"
                    bind:value={form.title.romaji}
                    placeholder="Title in romaji"
                />
                <Field.Error>{form.errors['title.romaji']}</Field.Error>
            </Field.Field>
            <Field.Field>
                <Field.Label for="title_native">Title (Native)</Field.Label>
                <Input
                    id="title_native"
                    bind:value={form.title.native}
                    placeholder="Title in native script"
                />
                <Field.Error>{form.errors['title.native']}</Field.Error>
            </Field.Field>
        </div>

        <Field.Field>
            <Field.Label for="description_lang">Description</Field.Label>
            <InputGroup.Root>
                <InputGroup.Textarea
                    id="description_lang"
                    bind:value={form.description[currentLang]}
                    placeholder={`Description in ${languages.find((l) => l.value === currentLang)?.label}`}
                    rows={6}
                />
                <InputGroup.Addon align="block-start" class="border-b">
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
                                    <Icon
                                        lucide={() =>
                                            import('lucide-svelte/icons/chevron-down')}
                                        tabler={() =>
                                            import('@tabler/icons-svelte/icons/chevron-down')}
                                        hugeicons={() =>
                                            import('@hugeicons/core-free-icons/ArrowDown01Icon')}
                                        phosphor={() =>
                                            import('phosphor-svelte/lib/CaretDownIcon')}
                                        remixicon={() =>
                                            import('remixicon-svelte/icons/arrow-down-s-line')}
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
                                        {#if currentLang === lang.value}
                                            <Icon
                                                lucide={() =>
                                                    import('lucide-svelte/icons/check')}
                                                tabler={() =>
                                                    import('@tabler/icons-svelte/icons/check')}
                                                hugeicons={() =>
                                                    import('@hugeicons/core-free-icons/Tick02Icon')}
                                                phosphor={() =>
                                                    import('phosphor-svelte/lib/CheckIcon')}
                                                remixicon={() =>
                                                    import('remixicon-svelte/icons/check-line')}
                                                class="size-4"
                                            />
                                        {/if}
                                        <span>{lang.label}</span>
                                    </div>
                                </DropdownMenu.Item>
                            {/each}
                        </DropdownMenu.Content>
                    </DropdownMenu.Root>
                </InputGroup.Addon>
            </InputGroup.Root>
            <Field.Error
                >{form.errors.description ||
                    form.errors[`description.${currentLang}`]}</Field.Error
            >
        </Field.Field>
    </div>
{/snippet}

{#snippet streamingAction()}
    {#if availableStreamTypes.length > 0}
        <DropdownMenu.Root>
            <DropdownMenu.Trigger>
                {#snippet child({ props: triggerProps })}
                    <Button variant="outline" size="sm" {...triggerProps}>
                        <Icon
                            lucide={() => import('lucide-svelte/icons/plus')}
                            tabler={() =>
                                import('@tabler/icons-svelte/icons/plus')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/PlusSignIcon')}
                            phosphor={() =>
                                import('phosphor-svelte/lib/PlusIcon')}
                            remixicon={() =>
                                import('remixicon-svelte/icons/add-line')}
                            class="mr-1 size-4"
                        />
                        Add Stream
                    </Button>
                {/snippet}
            </DropdownMenu.Trigger>
            <DropdownMenu.Content class="min-w-40">
                {#each availableStreamTypes as st (st.value)}
                    <DropdownMenu.Item onclick={() => addStream(st.value)}>
                        <span>{st.label}</span>
                    </DropdownMenu.Item>
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
            <p class="text-sm text-muted-foreground">No streams configured.</p>
        {/if}
    </div>
{/snippet}

{#snippet downloadLinksAction()}
    <Button variant="outline" size="sm" onclick={addLink}>
        <Icon
            lucide={() => import('lucide-svelte/icons/plus')}
            tabler={() => import('@tabler/icons-svelte/icons/plus')}
            hugeicons={() => import('@hugeicons/core-free-icons/PlusSignIcon')}
            phosphor={() => import('phosphor-svelte/lib/PlusIcon')}
            remixicon={() => import('remixicon-svelte/icons/add-line')}
            class="mr-1 size-4"
        />
        Add File
    </Button>
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
                            placeholder="Filename (e.g. 1080p x265)"
                        />
                        <Button
                            variant="ghost"
                            size="icon"
                            class="text-destructive shrink-0"
                            onclick={() => deleteLink(i)}
                        >
                            <Icon
                                lucide={() =>
                                    import('lucide-svelte/icons/trash-2')}
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/trash')}
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/Delete02Icon')}
                                phosphor={() =>
                                    import('phosphor-svelte/lib/TrashIcon')}
                                remixicon={() =>
                                    import('remixicon-svelte/icons/delete-bin-line')}
                                class="size-4"
                            />
                        </Button>
                    </Field.Field>
                    {#if link.value.length === 0}
                        <p class="text-xs text-muted-foreground">No mirrors.</p>
                    {:else}
                        {@render mirrorFields(
                            form.links[i],
                            (j) => deleteMirror(i, j),
                            'Mirror name',
                            'https://...',
                        )}
                    {/if}
                    <Button
                        variant="outline"
                        size="sm"
                        onclick={() => addMirror(i)}
                    >
                        <Icon
                            lucide={() => import('lucide-svelte/icons/plus')}
                            tabler={() =>
                                import('@tabler/icons-svelte/icons/plus')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/PlusSignIcon')}
                            phosphor={() =>
                                import('phosphor-svelte/lib/PlusIcon')}
                            remixicon={() =>
                                import('remixicon-svelte/icons/add-line')}
                            class="mr-1 size-4"
                        />
                        Add Mirror
                    </Button>
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
                                placeholder="Filename (e.g. 1080p x265)"
                            />
                        </Field.Field>
                        {#if link.value.length === 0}
                            <p class="text-xs text-muted-foreground">
                                No mirrors.
                            </p>
                        {:else}
                            {@render mirrorFields(
                                form.links[i],
                                (j) => deleteMirror(i, j),
                                'Mirror name',
                                'https://...',
                            )}
                        {/if}
                        <Button
                            variant="outline"
                            size="sm"
                            onclick={() => addMirror(i)}
                        >
                            <Icon
                                lucide={() => import('lucide-svelte/icons/plus')}
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/plus')}
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/PlusSignIcon')}
                                phosphor={() =>
                                    import('phosphor-svelte/lib/PlusIcon')}
                                remixicon={() =>
                                    import('remixicon-svelte/icons/add-line')}
                                class="mr-1 size-4"
                            />
                            Add Mirror
                        </Button>
                    </Card.Content>
                </Card.Root>
            {/if}
        {/each}
        {#if form.links.length === 0}
            <p class="text-sm text-muted-foreground">No files added.</p>
        {/if}
    </div>
{/snippet}

{#snippet thumbnailAction()}
    <MediaManager title="Cover / Thumbnail" bind:value={form.thumbnailItem}>
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
        <div class="overflow-hidden rounded-lg border">
            <img
                src={form.thumbnailItem.mediumUrl ?? form.thumbnailItem.url}
                alt={form.thumbnailItem.filename}
                class="w-full object-cover"
            />
        </div>
    {:else}
        <div
            class="flex h-32 flex-col items-center justify-center rounded-lg border border-dashed text-xs text-muted-foreground"
        >
            <Icon
                lucide={() => import('lucide-svelte/icons/image')}
                tabler={() => import('@tabler/icons-svelte/icons/photo')}
                hugeicons={() => import('@hugeicons/core-free-icons/Image01Icon')}
                phosphor={() => import('phosphor-svelte/lib/ImageIcon')}
                remixicon={() => import('remixicon-svelte/icons/image-line')}
                class="mb-2 size-8 opacity-40"
            />
            <span>Click header to open media picker</span>
        </div>
    {/if}
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
            disabled={!canPublish}
        />
        <Field.Error>{form.errors.isPublished}</Field.Error>
    </Field.Field>
{/snippet}

<div class="p-4 w-full mx-auto max-w-400">
    <form onsubmit={submit}>
        <div class="grid gap-4 md:gap-6 md:grid-cols-12">
            <div class="md:col-span-8 space-y-6">
                {@render card('Basic Information', basicInfoContent)}
                {#if form.postType === 'mv'}
                    {@render card('Streaming', streamingContent, streamingAction)}
                {:else}
                    <TracksEditor bind:vgmdbData={form.vgmdbData} />
                {/if}
                {@render card(
                    'Download Links',
                    downloadLinksContent,
                    downloadLinksAction,
                )}
            </div>
            <div
                class="md:col-span-4 md:sticky md:top-4 md:self-start space-y-6"
            >
                {@render card(
                    'Cover / Thumbnail',
                    thumbnailContent,
                    thumbnailAction,
                )}
                {@render card('Publishing', publishingContent)}

                <div class="flex flex-col gap-2">
                    <Button type="submit" disabled={form.processing}>
                        <Icon
                            lucide={() => import('lucide-svelte/icons/save')}
                            tabler={() =>
                                import('@tabler/icons-svelte/icons/device-floppy')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/FloppyDiskIcon')}
                            phosphor={() =>
                                import('phosphor-svelte/lib/FloppyDiskIcon')}
                            remixicon={() =>
                                import('remixicon-svelte/icons/save-line')}
                            class="mr-2 size-4"
                        />
                        {post?.id ? 'Update' : 'Save'}
                    </Button>

                    {#if canPublish && !form.isPublished}
                        <Button
                            type="button"
                            variant="secondary"
                            onclick={publish}
                            disabled={form.processing}
                        >
                            Publish
                        </Button>
                    {/if}

                    {#if post?.id}
                        <Button
                            type="button"
                            variant="destructive"
                            onclick={deletePost}
                            disabled={form.processing}
                        >
                            <Icon
                                lucide={() =>
                                    import('lucide-svelte/icons/trash-2')}
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/trash')}
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/Delete02Icon')}
                                phosphor={() =>
                                    import('phosphor-svelte/lib/TrashIcon')}
                                remixicon={() =>
                                    import('remixicon-svelte/icons/delete-bin-line')}
                                class="mr-2 size-4"
                            />
                            Delete
                        </Button>
                    {/if}
                </div>
            </div>
        </div>
    </form>
</div>
