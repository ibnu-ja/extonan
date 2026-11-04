<script lang="ts">
    import { Link, router, setLayoutProps } from '@inertiajs/svelte';
    import { Icon } from '@/components/icon';
    import * as Accordion from '@/components/ui/accordion';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import * as Item from '@/components/ui/item';
    import * as SpeedDial from '@/components/ui/speed-dial/index.js';
    import * as Table from '@/components/ui/table';
    import { t, formatDate } from '@/lib/locale.svelte';
    import { getResolution } from '@/lib/resolution';
    import {
        index as albumIndex,
        show as albumShow,
        edit as albumEdit,
    } from '@/routes/album';
    import {
        destroy as shinraiDestroy,
        update as shinraiUpdate,
    } from '@/routes/shinrai';

    let {
        post,
        related = [],
        canPublish = false,
    }: App.Data.Music.MusicShowResponse = $props();

    const displayTitle = $derived(t(post.title));
    const isSingle = $derived(post.postType === 'single');
    const typeLabel = $derived(isSingle ? 'Single' : 'Album');

    const vgmdbData = $derived(post.metadata?.vgmdb_data);
    const artist = $derived(
        post.metadata?.artist ??
            vgmdbData?.performers?.[0]?.names?.en ??
            vgmdbData?.performers?.[0]?.names?.ja ??
            vgmdbData?.vocals?.[0]?.names?.en ??
            vgmdbData?.vocals?.[0]?.names?.ja ??
            null,
    );
    const catalog = $derived(
        post.metadata?.catalog ?? vgmdbData?.catalog ?? null,
    );
    const label = $derived(
        post.metadata?.label ??
            vgmdbData?.publisher?.names?.en ??
            vgmdbData?.publisher?.names?.ja ??
            null,
    );
    const releaseDate = $derived(
        post.metadata?.release_date ?? vgmdbData?.release_date ?? null,
    );
    const discs = $derived(
        Array.isArray(vgmdbData?.discs) ? vgmdbData.discs : [],
    );

    function getTrackTitle(
        names?: Record<string, string | null> | null,
        defaultTitle: string = '',
    ): string {
        if (!names) {
return defaultTitle;
}

        return (
            names.English ||
            names.Romaji ||
            names.Japanese ||
            names.en ||
            names.romaji ||
            names.ja ||
            names['ja-latn'] ||
            names.native ||
            (Object.values(names).find(Boolean) as string | undefined) ||
            defaultTitle
        );
    }

    $effect(() => {
        setLayoutProps({
            showHeading: false,
            breadcrumbs: [
                { title: typeLabel, href: albumIndex().url },
                { title: displayTitle, href: '' },
            ],
        });
    });
</script>

<svelte:head>
    <title>{displayTitle} - {typeLabel}</title>
</svelte:head>

<div class="w-full mx-auto lg:max-w-7xl xl:max-w-screen-2xl">
    <div class="px-4 py-6 pt-16 md:pt-6">
        <Link
            href={albumIndex().url}
            class="hover:text-foreground transition-colors text-sm text-muted-foreground md:hidden"
        >
            {typeLabel}
        </Link>
        <div class="flex flex-col md:flex-row gap-6 mt-3 items-start">
            <div class="shrink-0">
                {#if post.thumbnail}
                    <img
                        src={post.thumbnail.large ?? post.thumbnail.medium}
                        alt={displayTitle}
                        class="aspect-square size-44 md:size-52 rounded-xl object-cover shadow-md border"
                    />
                {:else}
                    <div
                        class="aspect-square size-44 md:size-52 rounded-xl bg-muted flex items-center justify-center border"
                    >
                        <Icon
                            lucide={() => import('lucide-svelte/icons/disc')}
                            tabler={() => import('@tabler/icons-svelte/icons/disc')}
                            phosphor={() => import('phosphor-svelte/lib/DiscIcon')}
                            remixicon={() =>
                                import('remixicon-svelte/icons/disc-line')}
                            class="size-16 opacity-40"
                        />
                    </div>
                {/if}
            </div>

            <div class="min-w-0 flex-1 space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <Badge variant="outline" class="text-xs">
                        {typeLabel}
                    </Badge>
                    {#if !post.isPublished}
                        <Badge variant="destructive" class="text-xs">
                            Draft
                        </Badge>
                    {/if}
                </div>

                <h1 class="text-2xl md:text-4xl font-bold font-heading">
                    {displayTitle}
                </h1>

                {#if artist}
                    <p class="text-base font-medium text-foreground/90">
                        {artist}
                    </p>
                {/if}

                <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-muted-foreground pt-1">
                    {#if catalog}
                        <div>
                            <span class="font-medium text-foreground">Catalog:</span>
                            {catalog}
                        </div>
                    {/if}
                    {#if label}
                        <div>
                            <span class="font-medium text-foreground">Label:</span>
                            {label}
                        </div>
                    {/if}
                    {#if releaseDate}
                        <div>
                            <span class="font-medium text-foreground">Release Date:</span>
                            {releaseDate}
                        </div>
                    {/if}
                </div>

                {#if post.publishedAt && post.author}
                    <p class="text-muted-foreground text-xs pt-1">
                        Posted {formatDate(post.publishedAt)} by {post.author.name}
                    </p>
                {/if}
            </div>
        </div>
    </div>

    <hr />

    <div class="grid gap-6 px-4 py-6 md:grid-cols-12">
        <div class="md:col-span-8 lg:col-span-8 space-y-6">
            {#if post.description && t(post.description)}
                <div
                    class="rounded-lg border bg-card p-4 text-sm leading-relaxed text-card-foreground whitespace-pre-wrap"
                >
                    {t(post.description)}
                </div>
            {/if}

            {#if discs.length > 0}
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold tracking-tight">Tracklist</h2>
                    {#each discs as disc, dIndex (disc.name ?? dIndex)}
                        <div class="rounded-lg border bg-card overflow-hidden">
                            {#if disc.name}
                                <div class="bg-muted/50 px-4 py-2 border-b font-medium text-sm flex items-center justify-between">
                                    <span>{disc.name}</span>
                                    {#if disc.disc_length}
                                        <span class="text-xs text-muted-foreground font-mono">{disc.disc_length}</span>
                                    {/if}
                                </div>
                            {/if}
                            <Table.Root>
                                <Table.Header>
                                    <Table.Row>
                                        <Table.Head class="w-12 text-center">#</Table.Head>
                                        <Table.Head>Title</Table.Head>
                                        {#if disc.tracks?.some((tr: any) => tr.artist)}
                                            <Table.Head class="hidden md:table-cell">Artist</Table.Head>
                                        {/if}
                                        <Table.Head class="w-24 text-right">Length</Table.Head>
                                    </Table.Row>
                                </Table.Header>
                                <Table.Body>
                                    {#each disc.tracks ?? [] as track, tIndex (tIndex)}
                                        {@const trackTitle = getTrackTitle(
                                            track.names,
                                            `Track ${tIndex + 1}`,
                                        )}
                                        <Table.Row>
                                            <Table.Cell class="text-center font-mono text-xs text-muted-foreground">
                                                {tIndex + 1}
                                            </Table.Cell>
                                            <Table.Cell class="font-medium text-sm">
                                                {trackTitle}
                                            </Table.Cell>
                                            {#if disc.tracks?.some((tr: any) => tr.artist)}
                                                <Table.Cell class="hidden md:table-cell text-sm text-muted-foreground">
                                                    {track.artist ?? ''}
                                                </Table.Cell>
                                            {/if}
                                            <Table.Cell class="text-right font-mono text-xs text-muted-foreground">
                                                {track.track_length ?? track.length ?? '--:--'}
                                            </Table.Cell>
                                        </Table.Row>
                                    {/each}
                                </Table.Body>
                            </Table.Root>
                        </div>
                    {/each}
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
                    More Albums & Singles
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
                                                        class="size-14 rounded-lg object-cover"
                                                    />
                                                {:else}
                                                    <div class="size-14 rounded-lg bg-muted flex items-center justify-center">
                                                        <Icon
                                                            lucide={() => import('lucide-svelte/icons/disc')}
                                                            tabler={() => import('@tabler/icons-svelte/icons/disc')}
                                                            phosphor={() => import('phosphor-svelte/lib/DiscIcon')}
                                                            remixicon={() => import('remixicon-svelte/icons/disc-line')}
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
                                            href={albumShow.url(item.id)}
                                            {...props}
                                            class="flex items-center gap-3 p-2 rounded-lg transition-colors hover:bg-muted/60"
                                        >
                                            <div class="shrink-0">
                                                {#if item.thumbnail}
                                                    <img
                                                        src={item.thumbnail.medium}
                                                        alt=""
                                                        class="size-14 rounded-lg object-cover"
                                                    />
                                                {:else}
                                                    <div class="size-14 rounded-lg bg-muted flex items-center justify-center">
                                                        <Icon
                                                            lucide={() => import('lucide-svelte/icons/disc')}
                                                            tabler={() => import('@tabler/icons-svelte/icons/disc')}
                                                            phosphor={() => import('phosphor-svelte/lib/DiscIcon')}
                                                            remixicon={() => import('remixicon-svelte/icons/disc-line')}
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
                        No other albums or singles available.
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
                        <Link href={albumEdit.url(post.id)} {...props}>
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
                        if (confirm(`Are you sure you want to delete this ${typeLabel}?`)) {
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
                                embed: null,
                                saluran: null,
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
