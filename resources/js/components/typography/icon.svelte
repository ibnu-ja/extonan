<script lang="ts">
    import { Icon } from '@/components/icon';
    import type { LazyIcon } from '@/components/icon';

    interface Props {
        icon?: LazyIcon;
        class?: string;
        size?: number | string;
        color?: string;
        strokeWidth?: number | string;
        [key: string]: any;
    }

    let { icon, class: className, ...restProps }: Props = $props();
</script>

<Icon lucide={icon} class={className} {...restProps} />
