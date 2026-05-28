<script lang="ts">
	import { page } from '@inertiajs/svelte';
	import type { Snippet } from 'svelte';
	import AppFooter from '@/components/app-footer.svelte';
	import AppSidebarHeader from '@/components/app-sidebar-header.svelte';
	import AppSidebar from '@/components/app-sidebar.svelte';
	import BottomNavBar from '@/components/bottom-nav-bar.svelte';
	import MobileTopBar from '@/components/mobile-top-bar.svelte';
	import * as Sidebar from '@/components/ui/sidebar/index.js';
	import { Toaster } from '@/components/ui/sonner';
	import type { BreadcrumbItem } from '@/types';

	let {
		breadcrumbs = [],
		children,
	}: {
		breadcrumbs?: BreadcrumbItem[];
		children?: Snippet;
	} = $props();

	const isOpen = $derived(page.props.sidebarOpen);

	let isMobile = $state(false);

	$effect(() => {
		if (typeof window === 'undefined') {
return;
}

		const mq = window.matchMedia('(max-width: 767px)');
		isMobile = mq.matches;
		const handler = (e: MediaQueryListEvent) => {
 isMobile = e.matches; 
};
		mq.addEventListener('change', handler);

		return () => mq.removeEventListener('change', handler);
	});
</script>

<Sidebar.Provider open={isOpen}>
	<AppSidebar />
	<Sidebar.Inset class="flex flex-col pb-16 md:pb-0 overflow-x-hidden">
		{#if isMobile}
			<div class="h-16"></div>
			<MobileTopBar {breadcrumbs} />
		{:else}
			<AppSidebarHeader {breadcrumbs} />
		{/if}
		<div class="flex flex-1 flex-col">
			{@render children?.()}
		</div>
		<AppFooter />
	</Sidebar.Inset>
	{#if isMobile}
		<BottomNavBar />
	{/if}
	<Toaster />
</Sidebar.Provider>
