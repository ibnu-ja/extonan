<script lang="ts">
    import { Icon } from '../index.js';

    let { class: className = 'size-4' }: { class?: string } = $props();
</script>

<Icon
    lucide="UserPlusIcon"
    tabler="IconUserPlus"
    hugeicons="UserAdd01Icon"
    phosphor="UserPlusIcon"
    remixicon="RiUserAddLine"
    class={className}
/>
