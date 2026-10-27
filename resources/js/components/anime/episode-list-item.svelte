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
        episode.permissions?.update ||
            episode.permissions?.delete ||
            episode.permissions?.publish,
    );
</script>

<Item.Root class="rounded-none md:rounded-[var(--radius)]">
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
                        class="size-10 rounded-[calc(var(--radius)-2px)] object-cover"
                    />
                {:else}
                    <div class="size-10 rounded-[calc(var(--radius)-2px)] bg-muted"></div>
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
                                    class="size-7"
                                    {...triggerProps}
                                    onclick={(e) => e.stopPropagation()}
                                >
                                    <Icon
                                        lucide="EllipsisVerticalIcon"
                                        tabler="IconDotsVertical"
                                        hugeicons="MoreVerticalCircle01Icon"
                                        phosphor="DotsThreeVerticalIcon"
                                        remixicon="RiMore2Line"
                                        class="size-3.5"
                                    />
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
                                    <Icon
                                        lucide="PencilIcon"
                                        tabler="IconPencil"
                                        hugeicons="PencilEdit02Icon"
                                        phosphor="PencilIcon"
                                        remixicon="RiEditLine"
                                        class="size-4"
                                    />
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
                                    <Icon
                                        lucide="Trash2Icon"
                                        tabler="IconTrash"
                                        hugeicons="Delete02Icon"
                                        phosphor="TrashIcon"
                                        remixicon="RiDeleteBinLine"
                                        class="size-4"
                                    />
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
                                    <Icon
                                        lucide="SendIcon"
                                        tabler="IconSend"
                                        hugeicons="SentIcon"
                                        phosphor="PaperPlaneRightIcon"
                                        remixicon="RiSendPlaneLine"
                                        class="size-4"
                                    />
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
