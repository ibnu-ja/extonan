<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import { EllipsisVertical, Pencil, Send, Trash2 } from 'lucide-svelte';
    import { AspectRatio } from '@/components/ui/aspect-ratio';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import * as Card from '@/components/ui/card';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import { t, formatDate } from '@/lib/locale.svelte';
    import {
        show as postShow,
        edit as postEdit,
        destroy as postDestroy,
        update as postUpdate,
    } from '@/routes/post';

    let {
        episode,
        animeId,
        animeTitle,
    }: {
        episode: App.Data.Anime.EpisodeShowData | App.Data.EpisodeSummaryData;
        animeId: number;
        animeTitle?: Record<string, string | null>;
    } = $props();

    const hasAnyAction = $derived(
        episode.permissions?.update || episode.permissions?.delete || episode.permissions?.publish,
    );
</script>

<div class="relative">
    <Link href={postShow.url({ anime: animeId, post: episode.id })} class="group">
        <Card.Root
            class="ring-0 overflow-hidden py-0 gap-0 rounded-none md:rounded-xl transition-colors hover:bg-muted"
        >
            {#if episode.thumbnail}
                <AspectRatio ratio={16 / 9}>
                    <img
                        src={episode.thumbnail.medium}
                        alt={t(episode.title)}
                        loading="lazy"
                        class="h-full w-full object-cover transition-transform group-hover:scale-105"
                    />
                </AspectRatio>
            {:else}
                <AspectRatio ratio={16 / 9}>
                    <div class="bg-muted flex h-full items-center justify-center">
                        <span class="text-muted-foreground text-lg font-medium">
                            {episode.epNo ? `EP ${episode.epNo}` : '#'}
                        </span>
                    </div>
                </AspectRatio>
            {/if}
            <Card.Content class="p-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        {#if animeTitle}
                            <p class="text-muted-foreground truncate text-xs font-medium">
                                {t(animeTitle)}
                            </p>
                        {/if}
                        <p class="truncate text-sm font-medium">
                            {#if episode.epNo}
                                <span class="text-muted-foreground"
                                    >EP {episode.epNo}:
                                </span>
                            {/if}
                            {t(episode.title)}
                        </p>
                        {#if episode.publishedAt}
                            <p class="text-muted-foreground mt-0.5 text-xs">
                                {formatDate(episode.publishedAt)}
                            </p>
                        {/if}
                    </div>
                    {#if !episode.isPublished}
                        <Badge variant="destructive" class="shrink-0">Draft</Badge>
                    {/if}
                </div>
            </Card.Content>
        </Card.Root>
    </Link>

    {#if hasAnyAction}
        <div class="absolute right-2 top-2 z-10">
            <DropdownMenu>
                <DropdownMenuTrigger>
                    {#snippet child({ props })}
                        <Button
                            variant="secondary"
                            size="icon"
                            class="size-7 rounded-lg"
                            {...props}
                        >
                            <EllipsisVertical class="size-3.5" />
                        </Button>
                    {/snippet}
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-40">
                    {#if episode.permissions?.update}
                        <DropdownMenuItem
                            onclick={() =>
                                router.visit(
                                    postEdit.url({
                                        anime: animeId,
                                        post: episode.id,
                                    }),
                                )}
                        >
                            <Pencil class="size-4" />
                            Edit
                        </DropdownMenuItem>
                    {/if}
                    {#if episode.permissions?.delete}
                        <DropdownMenuItem
                            variant="destructive"
                            onclick={() => {
                                if (confirm('Delete this episode?')) {
                                    router.delete(
                                        postDestroy.url({
                                            anime: animeId,
                                            post: episode.id,
                                        }),
                                    );
                                }
                            }}
                        >
                            <Trash2 class="size-4" />
                            Delete
                        </DropdownMenuItem>
                    {/if}
                    {#if !episode.isPublished && episode.permissions?.publish}
                        <DropdownMenuItem
                            onclick={() =>
                                router.put(
                                    postUpdate.url({
                                        anime: animeId,
                                        post: episode.id,
                                    }),
                                    { isPublished: true },
                                )}
                        >
                            <Send class="size-4" />
                            Publish
                        </DropdownMenuItem>
                    {/if}
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    {/if}
</div>
