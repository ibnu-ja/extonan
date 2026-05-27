<script lang="ts">
	import AppLogoIcon from '@/components/AppLogoIcon.svelte';

	const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
</script>

<div class="flex items-center gap-2">
	<AppLogoIcon class="size-8" />
	<span class="truncate text-sm font-semibold">{appName}</span>
</div>
