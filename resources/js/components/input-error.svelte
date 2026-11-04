<script lang="ts">
    import { cn } from '@/lib/utils';

    let {
        message = '',
        class: className = '',
    }: {
        message?: string;
        class?: string;
    } = $props();
</script>

{#if message}
    <div class={cn('text-sm text-destructive', className)}>
        <p>{message}</p>
    </div>
{/if}
