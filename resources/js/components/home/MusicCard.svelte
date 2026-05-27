<script lang="ts">
	import { Link } from '@inertiajs/svelte';
	import { Badge } from '@/components/ui/badge';
	import { Button } from '@/components/ui/button';
	import PencilIcon from '@lucide/svelte/icons/pencil';
	import TrashIcon from '@lucide/svelte/icons/trash';
	import { cn } from '@/lib/utils';

	let {
		id,
		title,
		thumbnail,
		author,
		publishedAt,
		isPublished,
		link,
		permissions,
		aspect = 'square',
	}: {
		id: number;
		title: Record<string, string | null>;
		thumbnail: { extraLarge: string; large: string; medium: string; color: string } | null;
		author: { id: number; name: string | null; avatar: string | null } | null;
		publishedAt: string | null;
		isPublished: boolean;
		link: string;
		permissions: { update: boolean; delete: boolean; publish: boolean };
		aspect?: 'square' | 'video';
	} = $props();

	const displayTitle = $derived(title.en || title.romaji || title.native || '');
	const dateLabel = $derived(
		publishedAt
			? new Date(publishedAt).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
			: '',
	);
</script>

<Link href={link} class="flex flex-col gap-2">
	<div class={cn('relative overflow-hidden rounded-lg bg-muted', aspect === 'video' ? 'aspect-video' : 'aspect-square')}>
		{#if thumbnail}
			<img
				src={thumbnail.medium}
				alt={displayTitle}
				class="size-full object-cover"
				loading="lazy"
			/>
		{/if}
	</div>
	<div class="min-w-0">
		<p class="font-heading truncate text-sm font-medium">{displayTitle}</p>
		<p class="truncate text-xs text-muted-foreground">
			{#if !isPublished}
				<Badge variant="destructive" class="mr-1">Draft</Badge>
			{/if}
			{dateLabel}{#if author} &bull; {author.name}{/if}
		</p>
	</div>
	{#if permissions.update}
		<div class="flex gap-1">
			<Button variant="ghost" size="icon" href={`${link}/edit`}>
				<PencilIcon data-icon="inline-start" class="size-4" />
			</Button>
			<Button variant="ghost" size="icon">
				<TrashIcon data-icon="inline-start" class="size-4" />
			</Button>
		</div>
	{/if}
</Link>
