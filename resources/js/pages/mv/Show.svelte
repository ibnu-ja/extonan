<script lang="ts">
    import { Link, router, setLayoutProps } from '@inertiajs/svelte';
    import VideoPlayer from '@/components/anime/video-player.svelte';
    import { Icon } from '@/components/icon';
    import * as Accordion from '@/components/ui/accordion';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import * as DropdownMenu from '@/components/ui/dropdown-menu/index.js';
    import * as Item from '@/components/ui/item';
    import * as SpeedDial from '@/components/ui/speed-dial/index.js';
    import { t, formatDate } from '@/lib/locale.svelte';
    import { getResolution } from '@/lib/resolution';
    import { index as mvIndex, show as mvShow, edit as mvEdit } from '@/routes/mv';
    import {
        destroy as shinraiDestroy,
        update as shinraiUpdate,
    } from '@/routes/shinrai';

    let {
        post,
        related = [],
        canPublish = false,
    }: App.Data.Music.MusicShowResponse = $props();

    const streamOptions = $derived(
        (
            [
                post.embed ? { value: 'embed' as const, label: 'Embed' } : null,
                post.saluran
                    ? {
                          value: 'saluran' as const,
                          label: 'Stream (M3U8)',
                      }
                    : null,
            ] as const
        ).filter(
            (opt): opt is { value: 'embed' | 'saluran'; label: string } =>
                opt !== null,
        ),
    );

    // svelte-ignore state_referenced_locally
    let activeStreamType = $state<'embed' | 'saluran' | null>(
        post.embed ? 'embed' : post.saluran ? 'saluran' : null,
    );

    // svelte-ignore state_referenced_locally
    let selectedSaluran = $state(post.saluran?.value[0]?.value ?? null);

    // svelte-ignore state_referenced_locally
    let selectedEmbed = $state(post.embed?.value[0]?.value ?? null);

    const displayTitle = $derived(t(post.title));

    $effect(() => {
        setLayoutProps({
            showHeading: false,
            breadcrumbs: [
                { title: 'Music Video', href: mvIndex().url },
                { title: displayTitle, href: '' },
            ],
        });
    });
</script>

<svelte:head>
    <title>{displayTitle} - Music Video</title>
</svelte:head>

<div class="w-full mx-auto lg:max-w-7xl xl:max-w-screen-2xl">
    <div class="px-4 py-6 pt-16 md:pt-6">
        <Link
            href={mvIndex().url}
            class="hover:text-foreground transition-colors text-sm text-muted-foreground md:hidden"
        >
            Music Video
        </Link>
        <h1 class="text-2xl md:text-4xl font-bold font-heading mt-1">
            {displayTitle}
            {#if !post.isPublished}
                <Badge variant="destructive" class="ml-2 align-middle text-sm"
                    >Draft</Badge
                >
            {/if}
        </h1>
        {#if post.publishedAt && post.author}
            <p class="text-muted-foreground mt-2 text-sm">
                Published {formatDate(post.publishedAt)} by {post.author.name}
            </p>
        {/if}
    </div>

    <hr />

    <div class="grid gap-6 px-4 py-6 md:grid-cols-12">
        <div class="md:col-span-8 lg:col-span-8 space-y-6">
            {#if streamOptions.length > 0}
                <div class="flex items-center gap-2">
                    <DropdownMenu.Root>
                        <DropdownMenu.Trigger>
                            {#snippet child({ props: triggerProps })}
                                <Button
                                    variant="outline"
                                    size="sm"
                                    class="gap-1"
                                    {...triggerProps}
                                >
                                    {streamOptions.find(
                                        (s) => s.value === activeStreamType,
                                    )?.label ?? 'Select'}
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
                                        class="size-3 opacity-50"
                                    />
                                </Button>
                            {/snippet}
                        </DropdownMenu.Trigger>
                        <DropdownMenu.Content>
                            {#each streamOptions as opt (opt.value)}
                                <DropdownMenu.Item
                                    onclick={() =>
                                        (activeStreamType = opt.value)}
                                >
                                    <div class="flex w-full items-center gap-2">
                                        {#if activeStreamType === opt.value}
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
                                        <span>{opt.label}</span>
                                    </div>
                                </DropdownMenu.Item>
                            {/each}
                        </DropdownMenu.Content>
                    </DropdownMenu.Root>
                </div>

                {#if activeStreamType === 'embed' && post.embed && selectedEmbed}
                    <div
                        class="relative aspect-video w-full overflow-hidden rounded-xl bg-black flex items-center justify-center border"
                    >
                        {#if selectedEmbed.trim().startsWith('<iframe')}
                            <!--eslint-disable-next-line svelte/no-at-html-tags-->
                            {@html selectedEmbed}
                        {:else}
                            <iframe
                                src={selectedEmbed}
                                title={displayTitle}
                                class="h-full w-full border-0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen
                            ></iframe>
                        {/if}
                    </div>
                    {#if post.embed.value.length > 1}
                        <div class="flex flex-wrap gap-2">
                            {#each post.embed.value as stream (stream.value)}
                                <button
                                    class="rounded-[var(--radius)] border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted"
                                    class:bg-primary={selectedEmbed ===
                                        stream.value}
                                    class:text-primary-foreground={selectedEmbed ===
                                        stream.value}
                                    onclick={() =>
                                        (selectedEmbed = stream.value)}
                                >
                                    {stream.name}
                                </button>
                            {/each}
                        </div>
                    {/if}
                {:else if activeStreamType === 'saluran' && post.saluran && selectedSaluran}
                    <div class="aspect-video w-full rounded-xl overflow-hidden border bg-black">
                        <VideoPlayer
                            src={selectedSaluran}
                            poster={post.thumbnail?.extraLarge ??
                                post.thumbnail?.large ??
                                post.thumbnail?.medium}
                        />
                    </div>
                    {#if post.saluran.value.length > 1}
                        <div class="flex flex-wrap gap-2">
                            {#each post.saluran.value as stream (stream.value)}
                                <button
                                    class="rounded-[var(--radius)] border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted"
                                    class:bg-primary={selectedSaluran ===
                                        stream.value}
                                    class:text-primary-foreground={selectedSaluran ===
                                        stream.value}
                                    onclick={() =>
                                        (selectedSaluran = stream.value)}
                                >
                                    {stream.name}
                                </button>
                            {/each}
                        </div>
                    {/if}
                {/if}
            {:else}
                <div
                    class="flex aspect-video w-full flex-col items-center justify-center rounded-xl bg-muted/40 p-8 text-center text-muted-foreground border"
                >
                    <Icon
                        lucide={() => import('lucide-svelte/icons/video-off')}
                        tabler={() => import('@tabler/icons-svelte/icons/video-off')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/VideoOffIcon')}
                        phosphor={() => import('phosphor-svelte/lib/VideoCameraSlashIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/video-off-line')}
                        class="mb-3 size-12 opacity-60"
                    />
                    <p class="text-base font-medium">No video stream available</p>
                    <p class="text-xs text-muted-foreground/80 mt-1">
                        Check available download mirrors below.
                    </p>
                </div>
            {/if}

            {#if post.description && t(post.description)}
                <div
                    class="rounded-lg border bg-card p-4 text-sm leading-relaxed text-card-foreground whitespace-pre-wrap"
                >
                    {t(post.description)}
                </div>
            {/if}

            {#if post.links && post.links.length > 0}
                <div class="space-y-3">
                    <h2 class="text-lg font-semibold tracking-tight">Download Links</h2>
                    <Accordion.Root type="multiple" class="w-full">
                        {#each post.links as linkGroup (linkGroup.id)}
                            {@const res = getResolution(linkGroup.name)}
                            <Accordion.Item value={String(linkGroup.id)} class="border rounded-lg mb-2 px-3">
                                <Accordion.Trigger class="hover:no-underline py-3">
                                    <div class="flex items-center gap-2 text-left">
                                        <span class="font-medium text-sm">{linkGroup.name}</span>
                                        {#if res}
                                            <Badge variant={res.variant} class="text-xs">
                                                {res.label}
                                            </Badge>
                                        {/if}
                                    </div>
                                </Accordion.Trigger>
                                <Accordion.Content class="pt-2 pb-4">
                                    <div class="flex flex-wrap gap-2">
                                        {#each linkGroup.value as mirror (mirror.value)}
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                href={mirror.value}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                <Icon
                                                    lucide={() =>
                                                        import('lucide-svelte/icons/download')}
                                                    tabler={() =>
                                                        import('@tabler/icons-svelte/icons/download')}
                                                    hugeicons={() =>
                                                        import('@hugeicons/core-free-icons/Download01Icon')}
                                                    phosphor={() =>
                                                        import('phosphor-svelte/lib/DownloadSimpleIcon')}
                                                    remixicon={() =>
                                                        import('remixicon-svelte/icons/download-line')}
                                                    class="mr-1.5 size-3.5"
                                                />
                                                {mirror.name}
                                            </Button>
                                        {/each}
                                    </div>
                                </Accordion.Content>
                            </Accordion.Item>
                        {/each}
                    </Accordion.Root>
                </div>
            {/if}
        </div>

        <div class="md:col-span-4 lg:col-span-4">
            <div class="mb-3 px-0 md:px-4">
                <h4 class="text-lg font-semibold font-heading">
                    More Music Videos
                </h4>
            </div>

            <div class="max-h-128 overflow-y-auto px-0 md:px-4">
                {#if related.length > 0}
                    <Item.Group class="space-y-1">
                        {#each related as item (item.id)}
                            {@const isActive = item.id === post.id}
                            {@const slug = Object.values(item.slug).find(Boolean)}
                            <Item.Root class={isActive ? 'bg-muted' : ''} id={slug}>
                                {#snippet child({ props })}
                                    {#if isActive}
                                        <div {...props} class="flex items-center gap-3 p-2">
                                            <div class="shrink-0">
                                                {#if item.thumbnail}
                                                    <img
                                                        src={item.thumbnail.medium}
                                                        alt=""
                                                        class="h-11 w-18 rounded-[var(--radius)] object-cover"
                                                    />
                                                {:else}
                                                    <div class="h-11 w-18 rounded-[var(--radius)] bg-muted flex items-center justify-center">
                                                        <Icon
                                                            lucide={() => import('lucide-svelte/icons/video')}
                                                            tabler={() => import('@tabler/icons-svelte/icons/video')}
                                                            hugeicons={() => import('@hugeicons/core-free-icons/Video01Icon')}
                                                            phosphor={() => import('phosphor-svelte/lib/VideoIcon')}
                                                            remixicon={() => import('remixicon-svelte/icons/video-line')}
                                                            class="size-4 opacity-40"
                                                        />
                                                    </div>
                                                {/if}
                                            </div>
                                            <Item.Content class="min-w-0 flex-1">
                                                <Item.Title class="line-clamp-2 text-sm font-medium">
                                                    {t(item.title)}
                                                </Item.Title>
                                                {#if item.publishedAt}
                                                    <Item.Description class="text-xs text-muted-foreground">
                                                        {formatDate(item.publishedAt)}
                                                    </Item.Description>
                                                {/if}
                                            </Item.Content>
                                        </div>
                                    {:else}
                                        <Link
                                            href={mvShow.url(item.id)}
                                            {...props}
                                            class="flex items-center gap-3 p-2 rounded-lg transition-colors hover:bg-muted/60"
                                        >
                                            <div class="shrink-0">
                                                {#if item.thumbnail}
                                                    <img
                                                        src={item.thumbnail.medium}
                                                        alt=""
                                                        class="h-11 w-18 rounded-[var(--radius)] object-cover"
                                                    />
                                                {:else}
                                                    <div class="h-11 w-18 rounded-[var(--radius)] bg-muted flex items-center justify-center">
                                                        <Icon
                                                            lucide={() => import('lucide-svelte/icons/video')}
                                                            tabler={() => import('@tabler/icons-svelte/icons/video')}
                                                            hugeicons={() => import('@hugeicons/core-free-icons/Video01Icon')}
                                                            phosphor={() => import('phosphor-svelte/lib/VideoIcon')}
                                                            remixicon={() => import('remixicon-svelte/icons/video-line')}
                                                            class="size-4 opacity-40"
                                                        />
                                                    </div>
                                                {/if}
                                            </div>
                                            <Item.Content class="min-w-0 flex-1">
                                                <Item.Title class="line-clamp-2 text-sm font-medium">
                                                    {t(item.title)}
                                                </Item.Title>
                                                {#if item.publishedAt}
                                                    <Item.Description class="text-xs text-muted-foreground">
                                                        {formatDate(item.publishedAt)}
                                                    </Item.Description>
                                                {/if}
                                            </Item.Content>
                                        </Link>
                                    {/if}
                                {/snippet}
                            </Item.Root>
                        {/each}
                    </Item.Group>
                {:else}
                    <p class="text-muted-foreground text-sm">
                        No other music videos available.
                    </p>
                {/if}
            </div>
        </div>
    </div>
</div>

{#if post.permissions?.update || post.permissions?.delete || canPublish}
    <SpeedDial.Root>
        <SpeedDial.Trigger />
        <SpeedDial.Content>
            {#if post.permissions?.update}
                <SpeedDial.Item variant="default">
                    {#snippet child({ props })}
                        <Link href={mvEdit.url(post.id)} {...props}>
                            <Icon
                                lucide={() => import('lucide-svelte/icons/pencil')}
                                tabler={() => import('@tabler/icons-svelte/icons/pencil')}
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/PencilEdit02Icon')}
                                phosphor={() => import('phosphor-svelte/lib/PencilSimpleIcon')}
                                remixicon={() =>
                                    import('remixicon-svelte/icons/edit-line')}
                                class="size-4"
                            />
                            Edit
                        </Link>
                    {/snippet}
                </SpeedDial.Item>
            {/if}

            {#if post.permissions?.delete}
                <SpeedDial.Item
                    variant="destructive"
                    onclick={() => {
                        if (confirm('Are you sure you want to delete this Music Video?')) {
                            router.delete(shinraiDestroy.url(post.id));
                        }
                    }}
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
                    Delete
                </SpeedDial.Item>
            {/if}

            {#if !post.isPublished && canPublish}
                <SpeedDial.Item
                    variant="default"
                    onclick={() =>
                        router.put(
                            shinraiUpdate.url(post.id),
                            {
                                title: post.title,
                                description: post.description,
                                postType: post.postType,
                                isPublished: true,
                                links: post.links ?? [],
                                embed: post.embed,
                                saluran: post.saluran,
                                thumbnailItem: null,
                            },
                        )}
                >
                    <Icon
                        lucide={() => import('lucide-svelte/icons/send')}
                        tabler={() => import('@tabler/icons-svelte/icons/send')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/SentIcon')}
                        phosphor={() =>
                            import('phosphor-svelte/lib/PaperPlaneTiltIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/send-plane-line')}
                        class="size-4"
                    />
                    Publish
                </SpeedDial.Item>
            {/if}
        </SpeedDial.Content>
    </SpeedDial.Root>
{/if}
