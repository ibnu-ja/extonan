<script lang="ts">
	import { Link, page } from '@inertiajs/svelte';
	import CircleUserRound from 'lucide-svelte/icons/circle-user-round';
	import LayoutGrid from 'lucide-svelte/icons/layout-grid';
	import {
		Avatar,
		AvatarFallback,
	} from '@/components/ui/avatar';
	import { Button } from '@/components/ui/button';
	import {
		DropdownMenu,
		DropdownMenuContent,
		DropdownMenuItem,
		DropdownMenuTrigger,
	} from '@/components/ui/dropdown-menu';
	import UserMenuContent from '@/components/user-menu-content.svelte';
	import { toUrl } from '@/lib/utils';
	import { dashboard, login, register as registerRoute } from '@/routes';
	import type { BreadcrumbItem } from '@/types';

	let {
		breadcrumbs = [],
	}: {
		breadcrumbs?: BreadcrumbItem[];
	} = $props();

	const auth = $derived(page.props.auth);
	const canRegister = $derived(page.props.canRegister as boolean | undefined);

	let lastScrollY = $state(0);
	let hidden = $state(false);

	$effect(() => {
		if (typeof window === 'undefined') {
return;
}

		const onScroll = () => {
			const sy = window.scrollY;

			if (sy > lastScrollY && sy > 50) {
				hidden = true;
			} else if (sy < lastScrollY) {
				hidden = false;
			}

			lastScrollY = sy;
		};
		window.addEventListener('scroll', onScroll, { passive: true });

		return () => window.removeEventListener('scroll', onScroll);
	});
</script>

<header
	class="fixed top-0 left-0 right-0 z-50 flex h-16 items-center gap-2 border-b bg-background px-4 transition-transform duration-300"
	style={hidden ? 'transform: translateY(-100%);' : ''}
>
	{#if breadcrumbs && breadcrumbs.length > 0}
		<nav class="flex items-center gap-1 text-sm text-muted-foreground">
			{#each breadcrumbs as crumb, i (crumb.href)}
				{#if i > 0}
					<span class="text-muted-foreground/40">/</span>
				{/if}
				<span class={i === breadcrumbs.length - 1 ? 'font-medium text-foreground' : ''}>
					{crumb.title}
				</span>
			{/each}
		</nav>
	{/if}

	<div class="ml-auto flex items-center gap-2">
		{#if auth.user}
			<DropdownMenu>
				<DropdownMenuTrigger>
					{#snippet child({ props })}
						<Button
							variant="ghost"
							size="icon"
							class="relative size-9 rounded-full p-0"
							{...props}
						>
							<Avatar class="size-8">
								<AvatarFallback class="rounded-full text-xs font-medium">
									{auth.user.name?.charAt(0)?.toUpperCase() ?? '?'}
								</AvatarFallback>
							</Avatar>
						</Button>
					{/snippet}
				</DropdownMenuTrigger>
				<DropdownMenuContent align="end" class="w-56">
					<UserMenuContent user={auth.user}>
						<DropdownMenuItem>
							{#snippet child({ props })}
								<Link {...props} href={toUrl(dashboard())}>
									<LayoutGrid class="mr-2 h-4 w-4" />
									Dashboard
								</Link>
							{/snippet}
						</DropdownMenuItem>
					</UserMenuContent>
				</DropdownMenuContent>
			</DropdownMenu>
		{:else if canRegister}
			<DropdownMenu>
				<DropdownMenuTrigger>
					{#snippet child({ props })}
						<Button
							variant="ghost"
							size="icon"
							class="relative size-9 rounded-full p-0"
							{...props}
						>
							<CircleUserRound class="size-5 text-muted-foreground" />
						</Button>
					{/snippet}
				</DropdownMenuTrigger>
				<DropdownMenuContent align="end" class="w-40">
					<DropdownMenuItem>
						{#snippet child({ props })}
							<Link {...props} href={toUrl(login())}>
								Log in
							</Link>
						{/snippet}
					</DropdownMenuItem>
					<DropdownMenuItem>
						{#snippet child({ props })}
							<Link {...props} href={toUrl(registerRoute())}>
								Register
							</Link>
						{/snippet}
					</DropdownMenuItem>
				</DropdownMenuContent>
			</DropdownMenu>
		{:else}
			<Link href={toUrl(login())} class="inline-flex size-9 items-center justify-center rounded-full hover:bg-accent">
				<CircleUserRound class="size-5 text-muted-foreground" />
			</Link>
		{/if}
	</div>
</header>
