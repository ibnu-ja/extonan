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
        item.permissions?.update ||
            item.permissions?.delete ||
            item.permissions?.publish,
    );
</script>

<div class="relative">
    <Link
        href={animeShow.url(item.id)}
        class="group block overflow-hidden rounded-[var(--radius)] bg-muted"
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
            <h3
                class="text-sm font-medium leading-tight text-white line-clamp-2"
            >
                {displayTitle}
            </h3>
        </div>

        {#if !item.isPublished}
            <div class="absolute left-2 top-2">
                <Badge variant="secondary" class="gap-1 text-xs">
                    <Icon
                        lucide={() => import('lucide-svelte/icons/eye-off')}
                        tabler={() =>
                            import('@tabler/icons-svelte/icons/eye-off')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/ViewOffIcon')}
                        phosphor={() =>
                            import('phosphor-svelte/lib/EyeSlashIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/eye-off-line')}
                        class="size-3"
                    />
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
                            class="size-7"
                            {...props}
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
                                class="size-3.5"
                            />
                        </Button>
                    {/snippet}
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-40">
                    {#if item.permissions.update}
                        <DropdownMenuItem
                            onclick={() => router.visit(animeEdit(item.id).url)}
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
                    {#if item.permissions.delete}
                        <DropdownMenuItem
                            variant="destructive"
                            onclick={() => {
                                if (confirm('Delete this anime?')) {
                                    router.delete(animeDestroy(item.id).url);
                                }
                            }}
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
                    {#if !item.isPublished && item.permissions.publish}
                        <DropdownMenuItem
                            onclick={() =>
                                router.patch(animeUpdate(item.id).url, {
                                    isPublished: true,
                                })}
                        >
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
        </div>
    {/if}
</div>
