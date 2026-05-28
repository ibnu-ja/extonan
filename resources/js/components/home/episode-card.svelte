<script lang="ts">
	import { Link } from '@inertiajs/svelte';
	import PencilIcon from '@lucide/svelte/icons/pencil';
	import TrashIcon from '@lucide/svelte/icons/trash';
	import { Badge } from '@/components/ui/badge';
	import { Button } from '@/components/ui/button';
	import type {} from '@/types/generated';

	let {
		title,
		epNo,
		thumbnail,
		animeTitle,
		author,
		publishedAt,
		isPublished,
		link,
		permissions,
	}: {
		title: Record<string, string | null>;
		epNo: string | null;
		thumbnail: { extraLarge: string; large: string; medium: string; color: string } | null;
		animeTitle: Record<string, string | null>;
		author: { id: number; name: string | null; avatar: string | null } | null;
		publishedAt: string | null;
		isPublished: boolean;
		link: string;
		permissions: { update: boolean; delete: boolean; publish: boolean };
	} = $props();

	const displayTitle = $derived(title.en || title.romaji || title.native || '');
	const animeDisplayTitle = $derived(animeTitle.en || animeTitle.romaji || animeTitle.native || '');
	const subtitle = $derived(publishedAt ? `${new Date(publishedAt).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })}` : '');
	const episodeLabel = $derived(epNo ? `EP ${epNo} - ${displayTitle}` : displayTitle);
</script>

<div class="flex min-w-0 gap-3 rounded-xl border bg-card p-3 shadow-sm">
	{#if thumbnail}
		<img
			src={thumbnail.medium}
			alt={displayTitle}
			class="size-20 shrink-0 rounded-lg object-cover sm:size-24"
			loading="lazy"
		/>
	{/if}
	<div class="flex min-w-0 flex-1 flex-col justify-between gap-1">
		<div class="min-w-0">
			<p class="truncate text-xs text-muted-foreground">{animeDisplayTitle}</p>
			<Link href={link} class="font-heading block truncate font-medium hover:underline">
				{episodeLabel}
			</Link>
			{#if !isPublished}
				<Badge variant="destructive">Draft</Badge>
			{/if}
		</div>
		<div class="flex items-center justify-between gap-2">
			<p class="truncate text-xs text-muted-foreground">
				{subtitle}{#if author} &bull; {author.name}{/if}
			</p>
			{#if permissions.update}
				<div class="flex shrink-0 gap-1">
					<Button variant="ghost" size="icon" href={`${link}/edit`}>
						<PencilIcon data-icon="inline-start" class="size-4" />
					</Button>
					<Button variant="ghost" size="icon">
						<TrashIcon data-icon="inline-start" class="size-4" />
					</Button>
				</div>
			{/if}
		</div>
	</div>
</div>
