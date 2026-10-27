<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import { Icon } from '@/components/icon';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import * as Item from '@/components/ui/item';
    import * as Tooltip from '@/components/ui/tooltip';
    import {
        t,
        formatRelativeDate,
        formatFullDateTime,
    } from '@/lib/locale.svelte';
    import { cn, isEllipsisActive } from '@/lib/utils';
    import {
        show as albumShow,
        edit as albumEdit,
        destroy as albumDestroy,
        update as albumUpdate,
    } from '@/routes/album';
    import {
        show as mvShow,
        edit as mvEdit,
        destroy as mvDestroy,
        update as mvUpdate,
    } from '@/routes/mv';
    import {
        show as postShow,
        edit as postEdit,
        destroy as postDestroy,
        update as postUpdate,
    } from '@/routes/post';

    type PostItem = {
        id: number;
        title: Record<string, string | null>;
        thumbnail?: {
            medium?: string;
            large?: string;
            extraLarge?: string;
            color?: string;
        } | null;
        author?: {
            id: number;
            name: string | null;
            avatar: string | null;
        } | null;
        publishedAt?: string | null;
        isPublished: boolean;
        permissions?: {
            update?: boolean;
            delete?: boolean;
            publish?: boolean;
        };
        epNo?: string | null;
        postType?: string;
        animeId?: number;
        animeTitle?: Record<string, string | null>;
    };

    let {
        post,
        animeId,
        animeTitle,
        class: className,
    }: {
        post: PostItem;
        animeId?: number;
        animeTitle?: Record<string, string | null>;
        class?: string;
    } = $props();

    const resolvedAnimeId = $derived(animeId ?? post.animeId ?? 0);
    const resolvedAnimeTitle = $derived(animeTitle ?? post.animeTitle);

    const postType = $derived(
        post.postType ?? (resolvedAnimeId ? 'episode' : 'episode'),
    );

    const displayTitle = $derived.by(() => {
        const animeName = resolvedAnimeTitle ? t(resolvedAnimeTitle) : '';
        const epText = post.epNo ? `Episode ${post.epNo}` : '';

        if (animeName && epText) {
            return `${animeName} - ${epText}`;
        }

        if (epText) {
            return epText;
        }

        if (animeName && (!post.postType || post.postType === 'episode')) {
            return animeName;
        }

        return t(post.title);
    });

    const showUrl = $derived.by(() => {
        if (postType === 'mv') {
            return mvShow.url(post.id);
        }

        if (postType === 'album') {
            return albumShow.url(post.id);
        }

        if (resolvedAnimeId) {
            return postShow.url({ anime: resolvedAnimeId, post: post.id });
        }

        return '#';
    });

    const editUrl = $derived.by(() => {
        if (postType === 'mv') {
            return mvEdit.url(post.id);
        }

        if (postType === 'album') {
            return albumEdit.url(post.id);
        }

        if (resolvedAnimeId) {
            return postEdit.url({ anime: resolvedAnimeId, post: post.id });
        }

        return '#';
    });

    const hasAnyAction = $derived(
        Boolean(
            post.permissions?.update ||
            post.permissions?.delete ||
            post.permissions?.publish,
        ),
    );

    let isTruncated = $state(false);

    function detectEllipsis(node: HTMLElement) {
        const check = () => {
            isTruncated = isEllipsisActive(node);
        };

        check();

        const observer = new ResizeObserver(check);
        observer.observe(node);

        return {
            destroy() {
                observer.disconnect();
            },
        };
    }

    function handleDelete(e: MouseEvent) {
        e.stopPropagation();

        if (!confirm('Delete this item?')) {
            return;
        }

        if (postType === 'mv') {
            router.delete(mvDestroy.url(post.id));
        } else if (postType === 'album') {
            router.delete(albumDestroy.url(post.id));
        } else if (resolvedAnimeId) {
            router.delete(
                postDestroy.url({ anime: resolvedAnimeId, post: post.id }),
            );
        }
    }

    function handlePublish(e: MouseEvent) {
        e.stopPropagation();

        if (postType === 'mv') {
            router.put(mvUpdate.url(post.id), { isPublished: true });
        } else if (postType === 'album') {
            router.put(albumUpdate.url(post.id), { isPublished: true });
        } else if (resolvedAnimeId) {
            router.put(
                postUpdate.url({ anime: resolvedAnimeId, post: post.id }),
                { isPublished: true },
            );
        }
    }
</script>

<Item.Root
    class={cn(
        'group/post-list-item rounded-none md:rounded-[var(--radius)]',
        className,
    )}
>
    {#snippet child({ props: itemProps })}
        <Link
            href={showUrl}
            title={displayTitle}
            class="flex w-full items-center justify-between gap-3 p-2 focus-visible:outline-none"
            {...itemProps}
        >
            <!-- [image] -->
            <Item.Media variant="image">
                {#if post.thumbnail?.medium || post.thumbnail?.large || post.thumbnail?.extraLarge}
                    <img
                        src={post.thumbnail?.medium ??
                            post.thumbnail?.large ??
                            post.thumbnail?.extraLarge ??
                            ''}
                        alt=""
                        class="size-11 rounded-[calc(var(--radius)-2px)] object-cover"
                    />
                {:else}
                    <div
                        class="flex size-11 items-center justify-center rounded-[calc(var(--radius)-2px)] bg-muted text-xs font-medium text-muted-foreground"
                    >
                        {post.epNo ? `EP ${post.epNo}` : '#'}
                    </div>
                {/if}
            </Item.Media>

            <!-- [overline/title/subtitle] -->
            <Item.Content class="min-w-0 flex-1">
                <Tooltip.Provider delayDuration={200}>
                    <Tooltip.Root disabled={!isTruncated}>
                        <Tooltip.Trigger>
                            {#snippet child({ props: triggerProps })}
                                <Item.Title
                                    class="truncate font-medium"
                                    {...triggerProps}
                                >
                                    <span
                                        use:detectEllipsis
                                        class="block truncate"
                                    >
                                        {displayTitle}
                                    </span>
                                </Item.Title>
                            {/snippet}
                        </Tooltip.Trigger>
                        {#if isTruncated}
                            <Tooltip.Content
                                side="top"
                                class="max-w-xs text-xs font-normal"
                            >
                                {displayTitle}
                            </Tooltip.Content>
                        {/if}
                    </Tooltip.Root>
                </Tooltip.Provider>

                <Item.Description>
                    <div
                        class="flex flex-wrap items-center gap-1.5 text-xs text-muted-foreground"
                    >
                        {#if !post.isPublished}
                            <Badge
                                variant="destructive"
                                class="px-1.5 py-0 text-[10px] font-semibold leading-none shrink-0"
                            >
                                Draft
                            </Badge>
                        {/if}
                        {#if post.publishedAt}
                            <Tooltip.Provider delayDuration={200}>
                                <Tooltip.Root>
                                    <Tooltip.Trigger>
                                        {#snippet child({
                                            props: triggerProps,
                                        })}
                                            <time
                                                datetime={post.publishedAt}
                                                title={formatFullDateTime(
                                                    post.publishedAt ?? null,
                                                )}
                                                class="cursor-default hover:text-foreground transition-colors shrink-0"
                                                {...triggerProps}
                                            >
                                                {formatRelativeDate(
                                                    post.publishedAt ?? null,
                                                )}
                                            </time>
                                        {/snippet}
                                    </Tooltip.Trigger>
                                    <Tooltip.Content side="top">
                                        {formatFullDateTime(
                                            post.publishedAt ?? null,
                                        )}
                                    </Tooltip.Content>
                                </Tooltip.Root>
                            </Tooltip.Provider>
                        {/if}
                        {#if post.publishedAt && post.author}
                            <span class="select-none text-muted-foreground/60"
                                >&bull;</span
                            >
                        {/if}
                        {#if post.author}
                            <span class="truncate">{post.author.name}</span>
                        {/if}
                    </div>
                </Item.Description>
            </Item.Content>

            <!-- [action] -->
            <Item.Actions>
                {#if hasAnyAction}
                    <DropdownMenu>
                        <DropdownMenuTrigger>
                            {#snippet child({ props: triggerProps })}
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="size-7 text-muted-foreground hover:text-foreground"
                                    {...triggerProps}
                                    onclick={(e) => e.stopPropagation()}
                                >
                                    <Icon
                                        lucide={() =>
                                            import('lucide-svelte/icons/ellipsis-vertical')}
                                        tabler={() =>
                                            import('@tabler/icons-svelte/icons/dots-vertical')}
                                        hugeicons={() =>
                                            import('@hugeicons/core-free-icons/MoreVerticalCircle01Icon')}
                                        phosphor={() =>
                                            import('phosphor-svelte/lib/DotsThreeVerticalIcon')}
                                        remixicon={() =>
                                            import('remixicon-svelte/icons/more-2-line')}
                                        class="size-4"
                                    />
                                    <span class="sr-only">Actions</span>
                                </Button>
                            {/snippet}
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-40">
                            {#if post.permissions?.update && editUrl !== '#'}
                                <DropdownMenuItem
                                    onclick={(e) => {
                                        e.stopPropagation();
                                        router.visit(editUrl);
                                    }}
                                >
                                    <Icon
                                        lucide={() =>
                                            import('lucide-svelte/icons/pencil')}
                                        tabler={() =>
                                            import('@tabler/icons-svelte/icons/pencil')}
                                        hugeicons={() =>
                                            import('@hugeicons/core-free-icons/PencilEdit02Icon')}
                                        phosphor={() =>
                                            import('phosphor-svelte/lib/PencilIcon')}
                                        remixicon={() =>
                                            import('remixicon-svelte/icons/edit-line')}
                                        class="size-4"
                                    />
                                    Edit
                                </DropdownMenuItem>
                            {/if}
                            {#if post.permissions?.delete}
                                <DropdownMenuItem
                                    variant="destructive"
                                    onclick={handleDelete}
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
                                    Delete
                                </DropdownMenuItem>
                            {/if}
                            {#if !post.isPublished && post.permissions?.publish}
                                <DropdownMenuItem onclick={handlePublish}>
                                    <Icon
                                        lucide={() =>
                                            import('lucide-svelte/icons/send')}
                                        tabler={() =>
                                            import('@tabler/icons-svelte/icons/send')}
                                        hugeicons={() =>
                                            import('@hugeicons/core-free-icons/SentIcon')}
                                        phosphor={() =>
                                            import('phosphor-svelte/lib/PaperPlaneRightIcon')}
                                        remixicon={() =>
                                            import('remixicon-svelte/icons/send-plane-line')}
                                        class="size-4"
                                    />
                                    Publish
                                </DropdownMenuItem>
                            {/if}
                        </DropdownMenuContent>
                    </DropdownMenu>
                {/if}
            </Item.Actions>
        </Link>
    {/snippet}
</Item.Root>
