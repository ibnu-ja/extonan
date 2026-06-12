<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import { EllipsisVertical, Pencil, Send, Trash2 } from 'lucide-svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import * as Item from '@/components/ui/item';
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

<Item.Root class="rounded-none md:rounded-lg">
    {#snippet child({ props })}
        <Link
            href={postShow.url({ anime: animeId, post: episode.id })}
            {...props}
        >
            <Item.Media variant="image">
                {#if episode.thumbnail}
                    <img
                        src={episode.thumbnail.medium}
                        alt=""
                        class="size-10 rounded object-cover"
                    />
                {:else}
                    <div class="size-10 rounded bg-muted"></div>
                {/if}
            </Item.Media>
            <Item.Content>
                {#if animeTitle}
                    <Item.Description class="text-xs font-medium">
                        {t(animeTitle)}
                    </Item.Description>
                {/if}
                <Item.Title>
                    {#if episode.epNo}
                        <span class="text-muted-foreground"
                            >EP {episode.epNo}:
                        </span>
                    {/if}{t(episode.title)}
                </Item.Title>
                <Item.Description>
                    {formatDate(episode.publishedAt)}
                    {#if episode.author}
                        &bull; {episode.author.name}{/if}
                </Item.Description>
            </Item.Content>
            <Item.Actions>
                {#if hasAnyAction}
                    <DropdownMenu>
                        <DropdownMenuTrigger>
                            {#snippet child({ props: triggerProps })}
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="size-7 rounded-lg"
                                    {...triggerProps}
                                    onclick={(e) => e.stopPropagation()}
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
                {/if}
                {#if !episode.isPublished}
                    <Badge variant="destructive">Draft</Badge>
                {/if}
            </Item.Actions>
        </Link>
    {/snippet}
</Item.Root>
