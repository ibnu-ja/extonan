<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { EyeOff, Pencil } from 'lucide-svelte';
    import { Badge } from '@/components/ui/badge';
    import { edit as animeEdit, show as animeShow } from '@/routes/anime';

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
</script>

<Link
    href={animeShow.url(item.id)}
    class="group relative block overflow-hidden rounded-xl bg-muted"
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

    {#if item.permissions?.update}
        <div
            class="absolute right-2 top-2 opacity-0 transition-opacity group-hover:opacity-100"
        >
            <Link
                href={animeEdit.url(item.id)}
                prefetch="hover"
                class="inline-flex size-8 items-center justify-center rounded-lg border border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80"
            >
                <Pencil class="size-4" />
            </Link>
        </div>
    {/if}
</Link>
