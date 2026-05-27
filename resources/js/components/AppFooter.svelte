<script lang="ts">
	import { page } from '@inertiajs/svelte';
	import { Separator } from '@/components/ui/separator';
	import { siFacebook, siDiscord, siGithub } from 'simple-icons';

	const links = [
		{ url: 'https://facebook.com/extonan', title: 'Facebook', icon: siFacebook.path },
		{ url: 'https://discord.extonan.id', title: 'Discord', icon: siDiscord.path },
		{ url: 'https://github.com/shinrai', title: 'GitHub', icon: siGithub.path },
	];

	const props = $derived(page.props as Record<string, unknown>);
	const appVersion = $derived(props.appVersion as string);
	const appBranch = $derived(props.appBranch as string);
	const appCommitHash = $derived(props.appCommitHash as string);
	const appGitOriginRepo = $derived((props.appGitOriginRepo as string) ?? '');

	const repoBase = $derived(appGitOriginRepo.replace(/\.git$/, ''));
	const branchURL = $derived(`${repoBase}/tree/${appBranch}`);
	const commitURL = $derived(`${repoBase}/commit/${appCommitHash}`);
	const year = $derived(new Date().getFullYear());
</script>

<footer class="mt-auto py-6">
	<div class="mx-auto flex max-w-screen-2xl flex-col items-center gap-3 px-2 pb-3 text-center text-xs text-muted-foreground sm:px-4">
		<div class="flex gap-4">
			{#each links as link}
				<a href={link.url} target="_blank" rel="noopener noreferrer" class="hover:text-foreground" aria-label={link.title}>
					<svg viewBox="0 0 24 24" class="size-5 fill-current">
						<path d={link.icon} />
					</svg>
				</a>
			{/each}
		</div>
	</div>

	<Separator />

	<div class="mx-auto flex max-w-screen-2xl flex-col items-center gap-1 px-2 pt-3 text-center text-xs text-muted-foreground sm:px-4">
		<p>
			Running extonan Web <b class="font-semibold text-foreground">{appVersion}</b> on
			branch <a href={branchURL} target="_blank" rel="noopener noreferrer" class="underline hover:text-foreground">{appBranch}</a>
			(<a href={commitURL} target="_blank" rel="noopener noreferrer" class="underline hover:text-foreground">{appCommitHash}</a>)
		</p>
		<p>Copyright &copy; {year} IJI All Rights Reserved.</p>
	</div>
</footer>
