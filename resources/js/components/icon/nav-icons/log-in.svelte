<script lang="ts">
    import { Icon } from '../index.js';

    let { class: className = 'size-4' }: { class?: string } = $props();
</script>

<Icon
    lucide="LogInIcon"
    tabler="IconLogin"
    hugeicons="Login01Icon"
    phosphor="SignInIcon"
    remixicon="RiLoginBoxLine"
    class={className}
/>
