<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import type { Snippet } from 'svelte';
    import Heading from '@/components/heading.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Separator } from '@/components/ui/separator';
    import { currentUrlState } from '@/lib/current-url.svelte';
    import { toUrl } from '@/lib/utils';
    import { edit as editAppearance } from '@/routes/appearance';
    import { edit as editProfile } from '@/routes/profile';
    import { edit as editSecurity } from '@/routes/security';
    import type { NavItem } from '@/types';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();

    const auth = $derived(page.props.auth);

    const sidebarNavItems = $derived<NavItem[]>([
        ...(auth?.user
            ? [
                  {
                      title: 'Profile',
                      href: editProfile(),
                  },
                  {
                      title: 'Security',
                      href: editSecurity(),
                  },
              ]
            : []),
        {
            title: 'Appearance',
            href: editAppearance(),
        },
    ]);

    const url = currentUrlState();
</script>

<div class="px-4 py-6">
    <Heading
        title="Settings"
        description={auth?.user
            ? 'Manage your account and interface preferences'
            : 'Manage your application preferences'}
    />

    <div class="flex flex-col lg:flex-row lg:space-x-12">
        <aside class="w-full max-w-xl lg:w-48">
            <nav
                class="flex flex-col space-y-1 space-x-0"
                aria-label="Settings"
            >
                {#each sidebarNavItems as item (toUrl(item.href))}
                    {@const active = url.isCurrentUrl(
                        item.href,
                        url.currentUrl,
                    )}
                    <Link
                        href={toUrl(item.href)}
                        data-slot="sidebar-menu-button"
                        data-sidebar="menu-button"
                        data-active={active ? 'true' : undefined}
                        class="inline-flex h-8 w-full items-center justify-start gap-1.5 whitespace-nowrap rounded-[var(--radius)] border border-transparent bg-clip-padding px-2.5 text-sm font-medium transition-all outline-none select-none {active
                            ? 'bg-primary-container text-on-primary-container font-medium'
                            : 'text-muted-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground'}"
                    >
                        {item.title}
                    </Link>
                {/each}
                <button
                    type="button"
                    disabled
                    aria-disabled="true"
                    data-slot="sidebar-menu-button"
                    data-sidebar="menu-button"
                    class="inline-flex h-8 w-full items-center justify-between gap-1.5 whitespace-nowrap rounded-[var(--radius)] border border-transparent bg-clip-padding px-2.5 text-sm font-medium transition-all outline-none select-none cursor-not-allowed opacity-60 text-muted-foreground"
                >
                    <span>Player</span>
                    <Badge
                        variant="outline"
                        class="text-[10px] px-1.5 py-0 h-4 font-normal"
                    >
                        TBA
                    </Badge>
                </button>
            </nav>
        </aside>

        <Separator class="my-6 lg:hidden" />

        <div class="flex-1 min-w-0">
            <section class="space-y-12">
                {@render children?.()}
            </section>
        </div>
    </div>
</div>
