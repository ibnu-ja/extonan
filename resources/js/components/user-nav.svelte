<script lang="ts">
	import { Link, page } from '@inertiajs/svelte';
	import CircleUserRound from 'lucide-svelte/icons/circle-user-round';
	import LayoutGrid from 'lucide-svelte/icons/layout-grid';
	import { Avatar, AvatarFallback } from '@/components/ui/avatar';
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

	const auth = $derived(page.props.auth);
	const canRegister = $derived(page.props.canRegister as boolean | undefined);
	const isMobile = $derived(typeof window !== 'undefined' && window.innerWidth < 768);

</script>

<div class="ml-auto flex items-center gap-2">
	{#if auth.user}
		<DropdownMenu>
			<DropdownMenuTrigger>
				{#snippet child({ props })}
					<Button variant="ghost" size="icon" class="relative size-9 rounded-full p-0" {...props}>
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
					{#if isMobile}
						<DropdownMenuItem>
							{#snippet child({ props })}
								<Link {...props} href={toUrl(dashboard())}>
									<LayoutGrid class="mr-2 h-4 w-4" />
									Dashboard
								</Link>
							{/snippet}
						</DropdownMenuItem>
					{/if}
				</UserMenuContent>
			</DropdownMenuContent>
		</DropdownMenu>
	{:else if canRegister}
		<DropdownMenu>
			<DropdownMenuTrigger>
				{#snippet child({ props })}
					<Button variant="ghost" size="icon" class="relative size-9 rounded-full p-0" {...props}>
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
