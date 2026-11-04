<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { destroy } from '@/actions/Laravel/Passkeys/Http/Controllers/PasskeyRegistrationController';
    import Heading from '@/components/heading.svelte';
    import { Icon } from '@/components/icon';
    import type { Passkey } from '@/types/auth';
    import PasskeyItem from './passkey-item.svelte';
    import PasskeyRegister from './passkey-register.svelte';

    export type Props = {
        canManagePasskeys?: boolean;
        passkeys?: Passkey[];
    };

    let { canManagePasskeys = false, passkeys = [] }: Props = $props();

    const handleDelete = (id: number, onError: () => void) => {
        router.delete(destroy.url(id), {
            preserveScroll: true,
            onError,
        });
    };

    const handleRegisterSuccess = () => {
        router.reload();
    };
</script>

{#if canManagePasskeys}
    <div class="space-y-6">
        <Heading
            variant="small"
            title="Passkeys"
            description="Manage your passkeys for passwordless sign-in"
        />

        <div
            class="overflow-hidden rounded-[var(--radius)] border border-border"
        >
            {#if passkeys.length > 0}
                {#each passkeys as passkey (passkey.id)}
                    <PasskeyItem {passkey} onDelete={handleDelete} />
                {/each}
            {:else}
                <div class="p-8 text-center">
                    <div
                        class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-[var(--radius)] bg-muted"
                    >
                        <Icon
                            lucide={() =>
                                import('lucide-svelte/icons/key-round')}
                            tabler={() =>
                                import('@tabler/icons-svelte/icons/key')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/Key01Icon')}
                            phosphor={() =>
                                import('phosphor-svelte/lib/KeyIcon')}
                            remixicon={() =>
                                import('remixicon-svelte/icons/key-line')}
                            class="size-7 text-muted-foreground"
                        />
                    </div>
                    <p class="font-medium">No passkeys yet</p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Add a passkey to sign in without a password
                    </p>
                </div>
            {/if}
        </div>

        <PasskeyRegister onSuccess={handleRegisterSuccess} />
    </div>
{/if}
