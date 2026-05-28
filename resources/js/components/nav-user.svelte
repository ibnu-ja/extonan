<script lang="ts">
	import { Link, page, router } from '@inertiajs/svelte';
	import ChevronsUpDownIcon from '@lucide/svelte/icons/chevrons-up-down';
	import LogOutIcon from '@lucide/svelte/icons/log-out';
	import SettingsIcon from '@lucide/svelte/icons/settings';
	import { Button } from '@/components/ui/button';
	import * as DropdownMenu from '@/components/ui/dropdown-menu/index.js';
	import * as Sidebar from '@/components/ui/sidebar/index.js';
	import { useSidebar } from '@/components/ui/sidebar/index.js';
	import UserInfo from '@/components/user-info.svelte';
	import { toUrl } from '@/lib/utils';
	import { logout } from '@/routes';
	import { edit } from '@/routes/profile';

	const user = $derived(page.props.auth.user);
	const sidebar = useSidebar();
</script>

{#if user}
	<Sidebar.Menu>
		<Sidebar.MenuItem>
			<DropdownMenu.Root>
				<DropdownMenu.Trigger>
					{#snippet child({ props })}
						<Sidebar.MenuButton
							size="lg"
							class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
							{...props}
						>
							<UserInfo {user} />
							<ChevronsUpDownIcon class="ms-auto size-4" />
						</Sidebar.MenuButton>
					{/snippet}
				</DropdownMenu.Trigger>
				<DropdownMenu.Content
					class="w-(--bits-dropdown-menu-anchor-width) min-w-56 rounded-lg"
					side={sidebar.state === 'collapsed' ? 'right' : 'top'}
					align="end"
					sideOffset={4}
				>
					<DropdownMenu.Label class="p-0 font-normal">
						<div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
							<UserInfo {user} showEmail={true} />
						</div>
					</DropdownMenu.Label>
					<DropdownMenu.Separator />
					<DropdownMenu.Group>
						<DropdownMenu.Item>
							{#snippet child({ props })}
								<Button variant="ghost" class="w-full justify-start gap-2">
									{#snippet child({ props: btnProps })}
										<Link {...props} {...btnProps} href={toUrl(edit())} prefetch>
											<SettingsIcon class="size-4" />
											Settings
										</Link>
									{/snippet}
								</Button>
							{/snippet}
						</DropdownMenu.Item>
					</DropdownMenu.Group>
					<DropdownMenu.Separator />
					<DropdownMenu.Item class="w-full">
						{#snippet child({ props })}
							<Button variant="ghost" class="w-full justify-start gap-2" {...props} onclick={() => router.post(logout().url)}>
								<LogOutIcon class="size-4" />
								Log out
							</Button>
						{/snippet}
					</DropdownMenu.Item>
				</DropdownMenu.Content>
			</DropdownMenu.Root>
		</Sidebar.MenuItem>
	</Sidebar.Menu>
{/if}
