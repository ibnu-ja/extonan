<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import { EllipsisVertical, EyeOff, Pencil, Send, Trash2 } from 'lucide-svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import {
        edit as animeEdit,
        show as animeShow,
        destroy as animeDestroy,
        update as animeUpdate,
    } from '@/routes/anime';

    type Item = {
        id: number;
        title: Record<string, string | null>;
        slug: Record<string, string | null>;
        coverImage: {
            extraLarge: string;
            large: string;
            medium: string;
            color: string;
        };
        isPublished: boolean;
        permissions: { update: boolean; delete: boolean; publish: boolean };
    };

    let {
        item,
    }: {
        item: Item;
    } = $props();

    const displayTitle = $derived(
        item.title?.romaji ??
            item.title?.en ??
            item.title?.native ??
            'Untitled',
    );

    const hasAnyAction = $derived(
        item.permissions?.update || item.permissions?.delete || item.permissions?.publish,
    );
</script>

<div class="relative">
    <Link
        href={animeShow.url(item.id)}
        class="group block overflow-hidden rounded-xl bg-muted"
    >
        <div class="aspect-3/4 overflow-hidden">
            <img
                src={item.coverImage?.large ?? item.coverImage?.medium ?? ''}
                alt={displayTitle}
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy"
            />
        </div>

        <div
            class="absolute inset-x-0 bottom-0 bg-linear-to-t from-black/80 to-transparent p-3 pt-8"
        >
            <h3 class="text-sm font-medium leading-tight text-white line-clamp-2">
                {displayTitle}
            </h3>
        </div>

        {#if !item.isPublished}
            <div class="absolute left-2 top-2">
                <Badge variant="secondary" class="gap-1 text-xs">
                    <EyeOff class="size-3" />
                    Draft
                </Badge>
            </div>
        {/if}
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
                    {#if item.permissions.update}
                        <DropdownMenuItem
                            onclick={() => router.visit(animeEdit(item.id).url)}
                        >
                            <Pencil class="size-4" />
                            Edit
                        </DropdownMenuItem>
                    {/if}
                    {#if item.permissions.delete}
                        <DropdownMenuItem
                            variant="destructive"
                            onclick={() => {
                                if (confirm('Delete this anime?')) {
                                    router.delete(animeDestroy(item.id).url);
                                }
                            }}
                        >
                            <Trash2 class="size-4" />
                            Delete
                        </DropdownMenuItem>
                    {/if}
                    {#if !item.isPublished && item.permissions.publish}
                        <DropdownMenuItem
                            onclick={() =>
                                router.patch(animeUpdate(item.id).url, {
                                    isPublished: true,
                                })}
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
