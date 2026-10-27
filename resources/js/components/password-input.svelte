<script lang="ts">
    import { Icon } from '@/components/icon';
    import { Input } from '@/components/ui/input';
    import { cn } from '@/lib/utils';

    let { class: className = '', ...rest } = $props();

    let showPassword = $state(false);
</script>

<div class="relative">
    <Input
        type={showPassword ? 'text' : 'password'}
        class={cn('pr-10', className)}
        {...rest}
    />
    <button
        type="button"
        onclick={() => (showPassword = !showPassword)}
        class="text-muted-foreground hover:text-foreground focus-visible:ring-ring absolute inset-y-0 right-0 flex items-center rounded-r-[var(--radius)] px-3 focus-visible:outline-none focus-visible:ring-[3px]"
        aria-label={showPassword ? 'Hide password' : 'Show password'}
        tabindex={-1}
    >
        {#if showPassword}
            <Icon
                lucide={() => import('lucide-svelte/icons/eye-off')}
                tabler={() => import('@tabler/icons-svelte/icons/eye-off')}
                hugeicons={() =>
                    import('@hugeicons/core-free-icons/ViewOffIcon')}
                phosphor={() => import('phosphor-svelte/lib/EyeSlashIcon')}
                remixicon={() => import('remixicon-svelte/icons/eye-off-line')}
                class="size-4"
            />
        {:else}
            <Icon
                lucide={() => import('lucide-svelte/icons/eye')}
                tabler={() => import('@tabler/icons-svelte/icons/eye')}
                hugeicons={() => import('@hugeicons/core-free-icons/ViewIcon')}
                phosphor={() => import('phosphor-svelte/lib/EyeIcon')}
                remixicon={() => import('remixicon-svelte/icons/eye-line')}
                class="size-4"
            />
        {/if}
    </button>
</div>
