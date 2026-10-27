<script lang="ts">
    import { Icon } from '@/components/icon';
    import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';

    let {
        errors = [],
        title = 'Something went wrong.',
    }: {
        errors: string[];
        title?: string;
    } = $props();

    const uniqueErrors = $derived([...new Set(errors)]);
</script>

<Alert variant="destructive">
    <Icon
        lucide={() => import('lucide-svelte/icons/circle-alert')}
        tabler={() => import('@tabler/icons-svelte/icons/alert-circle')}
        hugeicons={() => import('@hugeicons/core-free-icons/AlertCircleIcon')}
        phosphor={() => import('phosphor-svelte/lib/WarningCircleIcon')}
        remixicon={() => import('remixicon-svelte/icons/error-warning-line')}
        class="size-4"
    />
    <AlertTitle>{title}</AlertTitle>
    <AlertDescription>
        <ul class="list-inside list-disc text-sm">
            {#each uniqueErrors as error (error)}
                <li>{error}</li>
            {/each}
        </ul>
    </AlertDescription>
</Alert>
